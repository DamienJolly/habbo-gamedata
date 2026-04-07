<?php

namespace Habbo\Gamedata\Services;

use Habbo\Gamedata\Concerns\HasGameDataEndpoint;
use Habbo\Gamedata\Concerns\WritesGameDataXml;
use Habbo\Gamedata\Models\ProductData;
use Habbo\Gamedata\Support\GameDataConfig;
use Illuminate\Support\Facades\Cache;

class ProductDataService
{
    use HasGameDataEndpoint;
    use WritesGameDataXml;

    protected string $cacheKey;
    protected string $path;

    public function __construct()
    {
        $this->cacheKey = GameDataConfig::cacheKey('product_data');
        $this->path = GameDataConfig::path('product_data');
    }

    public function content(): string
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return $this->withWriter(function () {
                $this->startElement('productdata');

                $products = ProductData::query()
                    ->orderBy('code')
                    ->cursor();

                foreach ($products as $product) {
                    $this->startElement('product');
                    $this->writeAttribute('code', $product->code);

                    $this->writeNullableElement('name', $product->name);
                    $this->writeNullableElement('description', $product->description);

                    $this->endElement();
                }

                $this->endElement();
            });
        });
    }

    protected function clearDependentCaches(): void
    {
        app(ExternalVariablesService::class)->clearCache();
    }
}
