<?php

namespace App\Providers\Filament;

use App\Filament\Avatares\AvatarLocal;
use App\Filament\Widgets\RevistaContenidoOverview;
use App\Filament\Widgets\RevistaEnviosOverview;
use App\Filament\Widgets\RevistaEnviosRecientes;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->strictAuthorization()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->spa()
            ->brandName('Derecho UNASAM')
            ->brandLogo(fn () => view('filament.brand'))
            ->favicon(asset('img/escudo-unasam.png'))
            // El proveedor de serie pide el avatar a ui-avatars.com: la CSP del
            // proyecto lo bloqueaba (se veía el icono de imagen rota) y además
            // enviaba el nombre del usuario a un tercero en cada carga.
            ->defaultAvatarProvider(AvatarLocal::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                RevistaEnviosOverview::class,
                RevistaContenidoOverview::class,
                RevistaEnviosRecientes::class,
                // Sin FilamentInfoWidget: la tarjeta con la versión de Filament y
                // los enlaces a su documentación y su GitHub sirve al equipo que
                // desarrolla, no al que administra el portal. En un panel
                // institucional en producción solo ocupa sitio y anuncia la
                // versión exacta del framework a cualquiera que entre.
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
