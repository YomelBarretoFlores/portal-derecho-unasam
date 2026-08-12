<?php

namespace App\Models\Concerns;

use App\Enums\EditorialStatus;

trait HasEditorialWorkflow
{
    public static function bootHasEditorialWorkflow(): void
    {
        static::saving(function ($model): void {
            $publicationAttributes = $model->editorialPublicationAttributes();
            $status = $model->estado_editorial
                ?: (method_exists($model, 'resolveInitialEditorialStatus')
                    ? $model->resolveInitialEditorialStatus()
                    : (collect($publicationAttributes)
                        ->contains(fn (string $attribute): bool => (bool) $model->getAttribute($attribute))
                            ? EditorialStatus::Published->value
                            : EditorialStatus::Draft->value));
            $model->estado_editorial = $status;

            foreach ($publicationAttributes as $attribute) {
                $model->setAttribute($attribute, $status === EditorialStatus::Published->value);
            }
        });
    }

    /** @return array<int, string> */
    protected function editorialPublicationAttributes(): array
    {
        return ['publicado'];
    }
}
