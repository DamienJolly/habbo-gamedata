<?php

return [
    'routes' => [
        'auto_register' => true,
    ],
    'hashes' => [
        'path' => '/gamedata/hashes',
    ],
    'external_texts' => [
        'cache_key' => 'gamedata.external_flash_texts',
        'path' => '/gamedata/external_flash_texts',
    ],
    'external_variables' => [
        'cache_key' => 'gamedata.external_variables',
        'path' => '/gamedata/external_variables',
    ],
    'external_override_texts' => [
        'cache_key' => 'gamedata.external_flash_override_texts',
        'path' => '/gamedata/override/external_flash_override_texts',
    ],
    'product_data' => [
        'cache_key' => 'gamedata.productdata_xml',
        'path' => '/gamedata/productdata_xml',
    ],
    'figure_data' => [
        'cache_key' => 'gamedata.figuredata',
        'path' => '/gamedata/figuredata',
    ],
    'furni_data' => [
        'cache_key' => 'gamedata.furnidata_xml',
        'path' => '/gamedata/furnidata_xml',
    ],
];
