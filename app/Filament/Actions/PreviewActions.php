<?php

namespace App\Filament\Actions;

use App\Support\PreviewUrl;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class PreviewActions
{
    public static function preview(): Action
    {
        return Action::make('preview')
            ->label('Vista previa')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->visible(fn (Model $record): bool => PreviewUrl::available($record))
            ->url(fn (Model $record): string => PreviewUrl::for($record))
            ->openUrlInNewTab();
    }

    public static function published(): Action
    {
        return Action::make('viewPublished')
            ->label('Ver publicado')
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->visible(fn (Model $record): bool => PreviewUrl::publicUrl($record) !== null)
            ->url(fn (Model $record): string => PreviewUrl::publicUrl($record) ?? '#')
            ->openUrlInNewTab();
    }
}
