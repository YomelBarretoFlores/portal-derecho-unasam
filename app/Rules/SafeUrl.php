<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        $url = trim((string) $value);
        $isInternal = str_starts_with($url, '/') && ! str_starts_with($url, '//');
        $isHttps = filter_var($url, FILTER_VALIDATE_URL) !== false
            && strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https';

        if (! $isInternal && ! $isHttps) {
            $fail('El campo :attribute debe ser una ruta interna o una URL HTTPS.');
        }
    }
}
