<?php

namespace App\Filament\Widgets;

use App\Models\RevistaEnvio;
use App\Models\User;
use App\Services\RevistaSubmissionService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Estado de la cola editorial de manuscritos. Agrupa los estados de
 * RevistaEnvio::ESTADOS en las cuatro fases que le importan al editor.
 */
class RevistaEnviosOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Envíos de manuscritos';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isSuperAdmin() || $user->isEditor());
    }

    protected function getStats(): array
    {
        $porEstado = RevistaEnvio::query()
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $suma = fn (array $estados): int => (int) collect($estados)
            ->sum(fn (string $estado): int => (int) ($porEstado[$estado] ?? 0));

        $pendientes = $suma(['recibido', 'correccion_recibida']);
        $enRevision = $suma(['verificacion_documental', 'observado', 'revision_editorial', 'revision_pares']);

        // Un editor no debería tener que leer variables de entorno para saber
        // por qué el formulario público está cerrado.
        $motivos = app(RevistaSubmissionService::class)->unavailableReasons();
        $abierta = $motivos === [];

        return [
            Stat::make('Recepción pública', $abierta ? 'Abierta' : 'Cerrada')
                ->description($abierta ? 'El formulario acepta manuscritos' : $motivos[0])
                ->descriptionIcon($abierta ? 'heroicon-m-lock-open' : 'heroicon-m-lock-closed')
                ->color($abierta ? 'success' : 'gray'),
            Stat::make('Requieren atención', (string) $pendientes)
                ->description($pendientes > 0 ? 'Recibidos y correcciones sin abrir' : 'Nada en espera')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($pendientes > 0 ? 'warning' : 'gray'),
            Stat::make('En proceso editorial', (string) $enRevision)
                ->description('Verificación, observados y revisión por pares')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
            Stat::make('Aceptados', (string) $suma(['aceptado', 'publicado']))
                ->description('Aceptados y ya publicados')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Rechazados', (string) $suma(['rechazado']))
                ->description('Cerrados sin publicación')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
