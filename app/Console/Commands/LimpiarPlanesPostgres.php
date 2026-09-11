<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

/**
 * Descarta los planes de consulta que el pooler de PostgreSQL guarda en memoria.
 *
 * Hay que ejecutarlo después de cada migración que cambie columnas.
 *
 * El portal habla con PostgreSQL a través de un pooler, que mantiene vivas unas
 * pocas conexiones al servidor y las reparte entre todas las peticiones. Cada
 * una guarda el plan compilado de las consultas que ya ha visto, con la lista de
 * columnas del momento en que lo compiló. Cuando una migración añade una
 * columna, esos planes quedan obsoletos y PostgreSQL responde:
 *
 *     SQLSTATE[0A000]: cached plan must not change result type
 *
 * Como las consultas afectadas son las de cada visita, en pocos segundos todas
 * las conexiones del pooler tienen el plan viejo y el sitio entero devuelve 500.
 * Ocurrió el 11/09/2026 y no bastó con redesplegar: los planes no viven en la
 * aplicación, sino en el servidor, así que sobreviven al reinicio del contenedor.
 *
 * El remedio es DISCARD ALL, que vacía el estado de sesión —sentencias
 * preparadas incluidas— de la conexión que lo ejecuta. Como no se puede elegir a
 * cuál del pooler se cae, se abren varias y se comprueba después que las
 * consultas con parámetros vuelven a funcionar.
 *
 * Se intentó antes la vía aparentemente más limpia, desactivar las sentencias
 * preparadas con PDO::ATTR_EMULATE_PREPARES. No sirve: al emular, PHP envía los
 * booleanos como 1 y PostgreSQL rechaza «operator does not exist: boolean =
 * integer». Habría roto todas las consultas del portal, y la suite no lo habría
 * visto porque corre sobre SQLite.
 */
class LimpiarPlanesPostgres extends Command
{
    protected $signature = 'db:limpiar-planes
        {--conexiones=40 : Conexiones a abrir en cada ronda}
        {--rondas=3 : Intentos máximos antes de darse por vencido}';

    protected $description = 'Descarta los planes de consulta obsoletos del pooler de PostgreSQL tras una migración';

    public function handle(): int
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->line('No es PostgreSQL: no hay nada que descartar.');

            return self::SUCCESS;
        }

        $rondas = max(1, (int) $this->option('rondas'));

        for ($ronda = 1; $ronda <= $rondas; $ronda++) {
            if ($this->planesCorrectos()) {
                $this->info($ronda === 1
                    ? 'Los planes de consulta ya estaban al día.'
                    : "Planes descartados correctamente (ronda {$ronda}).");

                return self::SUCCESS;
            }

            $descartadas = $this->descartar();
            $this->line("  Ronda {$ronda}: DISCARD ALL en {$descartadas} conexiones.");
        }

        if ($this->planesCorrectos()) {
            $this->info('Planes descartados correctamente.');

            return self::SUCCESS;
        }

        $this->error('Siguen quedando planes obsoletos en el pooler.');
        $this->line('  Repita con más conexiones: php artisan db:limpiar-planes --conexiones=100');

        return self::FAILURE;
    }

    /**
     * Abre conexiones nuevas y vacía el estado de sesión de cada una.
     *
     * No usa la conexión de Laravel: esa es una sola, y lo que hay que alcanzar
     * son las varias que el pooler tiene abiertas contra el servidor.
     */
    private function descartar(): int
    {
        $c = config('database.connections.pgsql');
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
            $c['host'], $c['port'], $c['database'], $c['sslmode'] ?? 'prefer',
        );

        $hechas = 0;

        for ($i = 0; $i < (int) $this->option('conexiones'); $i++) {
            try {
                $pdo = new PDO($dsn, $c['username'], $c['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                $pdo->exec('DISCARD ALL');
                $hechas++;
                $pdo = null;
            } catch (Throwable) {
                // Una conexión rechazada no invalida la ronda: se sigue con las
                // demás y el recuento final dice cuántas se alcanzaron.
            }
        }

        return $hechas;
    }

    /**
     * ¿Responden ya las consultas con parámetros?
     *
     * Se repite varias veces porque cada intento cae en una conexión distinta
     * del pooler: una sola consulta correcta no demuestra que no quede ninguna
     * obsoleta.
     */
    private function planesCorrectos(): bool
    {
        $tablas = $this->tablasConColumnasRecientes();

        for ($i = 0; $i < 25; $i++) {
            foreach ($tablas as $tabla) {
                try {
                    DB::connection('pgsql')->select("select * from \"{$tabla}\" where 1 = ? limit 1", [1]);
                } catch (Throwable $e) {
                    if (str_contains($e->getMessage(), 'cached plan')) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Tablas que conviene comprobar: las que las migraciones tocan de verdad.
     *
     * @return array<int, string>
     */
    private function tablasConColumnasRecientes(): array
    {
        return array_values(array_filter(
            ['revistas', 'comunicados', 'blog_posts', 'docentes', 'organigrama', 'revista_numeros', 'articulos'],
            fn (string $tabla): bool => DB::connection('pgsql')->getSchemaBuilder()->hasTable($tabla),
        ));
    }
}
