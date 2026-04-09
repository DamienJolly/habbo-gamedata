<?php

namespace Habbo\Gamedata\Services;

use Habbo\Gamedata\Concerns\HasGameDataEndpoint;
use Habbo\Gamedata\Models\ExternalVariable;
use Habbo\Gamedata\Support\GameDataConfig;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Cache;

class ExternalVariablesService
{
    use HasGameDataEndpoint;

    protected string $cacheKey;
    protected string $path;

    public function __construct(private readonly Container $container)
    {
        $this->cacheKey = GameDataConfig::cacheKey('external_variables');
        $this->path = GameDataConfig::path('external_variables');
    }

    public function content(): string
    {
        return Cache::rememberForever($this->cacheKey, function () {
            $lines = ExternalVariable::query()
                ->orderBy('key')
                ->get()
                ->map(fn ($row) => "{$row->key}={$row->value}")
                ->toArray();

            if (! empty($lines)) {
                $lines[] = '';
            }

            $lines[] = 'flash.client.url=' . GameDataConfig::path('flash_client');
            $lines[] = 'new.user.flow.roomTypes=10,11,12';
            $lines[] = 'external.texts.txt=' . $this->resolveHashedUrl(ExternalTextsService::class);
            $lines[] = 'external.figurepartlist.txt=' . $this->resolveHashedUrl(FigureDataService::class);
            $lines[] = 'productdata.load.url=' . $this->resolveHashedUrl(ProductDataService::class);
            $lines[] = 'furnidata.load.url=' . $this->resolveHashedUrl(FurniDataService::class);
            $lines[] = 'external.override.texts.txt=' . $this->resolveHashedUrl(ExternalOverrideTextsService::class);

            return implode("\n", $lines);
        });
    }

    private function resolveHashedUrl(string $serviceClass): string
    {
        if (! class_exists($serviceClass)) {
            return '';
        }

        try {
            $service = $this->container->make($serviceClass);
        } catch (\Throwable) {
            return '';
        }

        if (! method_exists($service, 'hashedUrl')) {
            return '';
        }

        return (string) $service->hashedUrl();
    }
}
