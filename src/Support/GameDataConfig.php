<?php

namespace Habbo\Gamedata\Support;

class GameDataConfig
{
    private const DEFAULT_PATHS = [
        'hashes' => '/gamedata/hashes',
        'external_texts' => '/gamedata/external_flash_texts',
        'external_variables' => '/gamedata/external_variables',
        'external_override_texts' => '/gamedata/override/external_flash_override_texts',
        'product_data' => '/gamedata/productdata_xml',
        'figure_data' => '/gamedata/figuredata',
        'furni_data' => '/gamedata/furnidata_xml',
    ];

    private const DEFAULT_CACHE_KEYS = [
        'external_texts' => 'gamedata.external_flash_texts',
        'external_variables' => 'gamedata.external_variables',
        'external_override_texts' => 'gamedata.external_flash_override_texts',
        'product_data' => 'gamedata.productdata_xml',
        'figure_data' => 'gamedata.figuredata',
        'furni_data' => 'gamedata.furnidata_xml',
    ];

    public static function path(string $section): string
    {
        return (string) config('habbo-gamedata.' . $section . '.path', self::DEFAULT_PATHS[$section] ?? '');
    }

    public static function routePath(string $section): string
    {
        return ltrim(self::path($section), '/');
    }

    public static function cacheKey(string $section): string
    {
        return (string) config('habbo-gamedata.' . $section . '.cache_key', self::DEFAULT_CACHE_KEYS[$section] ?? '');
    }
}
