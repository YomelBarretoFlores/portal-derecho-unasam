<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RevistaEnvios\RevistaEnvioResource;
use App\Models\RevistaEnvio;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Últimos manuscritos recibidos, con acceso directo a su gestión. Muestra los
 * datos de contacto ya enmascarados por el modelo: el dashboard no expone el
 * documento de identidad ni el WhatsApp completos.
 */
class RevistaEnviosRecientes extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isSuperAdmin() || $user->isEditor());
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Últimos envíos recibidos')
            ->query(fn (): Builder => RevistaEnvio::query()->with('lineaInvestigacion'))
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Todavía no hay envíos')
            ->emptyStateDescription('Los manuscritos enviados desde el portal público aparecerán aquí.')
            ->columns([
                TextColumn::make('codigo_seguimiento')->label('Código')->searchable()->copyable(),
                TextColumn::make('nombres')->label('Autor')->searchable(),
                TextColumn::make('titulo')->label('Título')->limit(40)->wrap(),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (string $state): string => RevistaEnvio::ESTADOS[$state] ?? $state),
                TextColumn::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->recordActions([
                Action::make('gestionar')
                    ->label('Gestionar')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (RevistaEnvio $record): string => RevistaEnvioResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
