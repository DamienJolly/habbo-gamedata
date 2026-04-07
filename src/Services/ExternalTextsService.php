<?php

namespace Habbo\Gamedata\Services;

use Habbo\Gamedata\Concerns\HasGameDataEndpoint;
use Habbo\Gamedata\Models\ExternalText;
use Habbo\Gamedata\Support\GameDataConfig;
use Illuminate\Support\Facades\Cache;

class ExternalTextsService
{
    use HasGameDataEndpoint;

    protected string $cacheKey;
    protected string $path;

    public function __construct()
    {
        $this->cacheKey = GameDataConfig::cacheKey('external_texts');
        $this->path = GameDataConfig::path('external_texts');
    }

    public function content(): string
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return ExternalText::query()
                ->orderBy('key')
                ->get()
                ->map(fn ($row) => "{$row->key}={$row->value}")
                ->implode("\n");
        });
    }

    protected function clearDependentCaches(): void
    {
        app(ExternalVariablesService::class)->clearCache();
    }
}
