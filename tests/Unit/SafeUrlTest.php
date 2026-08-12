<?php

namespace Tests\Unit;

use App\Rules\SafeUrl;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SafeUrlTest extends TestCase
{
    #[DataProvider('validUrls')]
    public function test_it_accepts_internal_paths_and_https(string $url): void
    {
        $this->assertTrue($this->validator($url)->passes());
    }

    #[DataProvider('invalidUrls')]
    public function test_it_rejects_unsafe_or_unsupported_urls(string $url): void
    {
        $this->assertFalse($this->validator($url)->passes());
    }

    public static function validUrls(): array
    {
        return [['/plan-2023'], ['https://unasam.edu.pe/documento.pdf']];
    }

    public static function invalidUrls(): array
    {
        return [['javascript:alert(1)'], ['http://example.com'], ['//evil.example']];
    }

    private function validator(string $url): Validator
    {
        $factory = new Factory(new Translator(new ArrayLoader, 'es'));

        return $factory->make(['url' => $url], ['url' => [new SafeUrl]]);
    }
}
