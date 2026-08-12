<?php

namespace App\Enums;

enum EditorialStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Verified = 'verified';
    case Published = 'published';

    /** @return array<string, string> */
    public static function options(): array
    {
        return [
            self::Draft->value => 'Borrador',
            self::Pending->value => 'Pendiente de revisión',
            self::Verified->value => 'Verificado',
            self::Published->value => 'Publicado',
        ];
    }

    public static function color(?string $status): string
    {
        return match ($status) {
            self::Published->value => 'success',
            self::Verified->value => 'info',
            self::Pending->value => 'warning',
            default => 'gray',
        };
    }
}
