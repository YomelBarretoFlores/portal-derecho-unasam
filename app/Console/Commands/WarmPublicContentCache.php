<?php

namespace App\Console\Commands;

use App\Services\PublicContentCache;
use Illuminate\Console\Command;

class WarmPublicContentCache extends Command
{
    protected $signature = 'content:cache:warm';

    protected $description = 'Precarga la caché normalizada del contenido público';

    public function handle(PublicContentCache $content): int
    {
        $result = $content->warm();
        $this->info("Caché pública precargada (versión {$result['version']}) a las {$result['warmed_at']}.");

        return self::SUCCESS;
    }
}
