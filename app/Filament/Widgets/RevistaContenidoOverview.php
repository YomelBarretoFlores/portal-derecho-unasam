<?php

namespace App\Filament\Widgets;

use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaMiembro;
use App\Models\RevistaNumero;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Contenido editorial publicado frente al que sigue en borrador. Usa los mismos
 * scopes que el sitio público, de modo que lo que aquí aparece como publicado
 * es exactamente lo que el visitante ve.
 */
class RevistaContenidoOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Contenido de la revista';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isSuperAdmin() || $user->isEditor());
    }

    protected function getStats(): array
    {
        $numerosTotal = RevistaNumero::query()->count();
        $numerosPublicos = RevistaNumero::query()->publicados()->count();
        $articulosTotal = Articulo::query()->count();
        $articulosPublicos = Articulo::query()->publicados()->count();
        $fichaPublica = Revista::query()->publica()->exists();

        return [
            Stat::make('Números publicados', $numerosPublicos.' / '.$numerosTotal)
                ->description($numerosTotal - $numerosPublicos.' sin publicar')
                ->descriptionIcon('heroicon-m-book-open')
                ->color($numerosPublicos > 0 ? 'success' : 'gray'),
            Stat::make('Artículos publicados', $articulosPublicos.' / '.$articulosTotal)
                ->description($articulosTotal - $articulosPublicos.' sin publicar')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($articulosPublicos > 0 ? 'success' : 'gray'),
            Stat::make('Equipo editorial', (string) RevistaMiembro::query()->activos()->count())
                ->description('Miembros visibles en el portal')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Ficha de la revista', $fichaPublica ? 'Publicada' : 'No publicada')
                ->description($fichaPublica ? 'El micrositio es público' : 'El micrositio responde 404')
                ->descriptionIcon($fichaPublica ? 'heroicon-m-globe-alt' : 'heroicon-m-eye-slash')
                ->color($fichaPublica ? 'success' : 'danger'),
        ];
    }
}
