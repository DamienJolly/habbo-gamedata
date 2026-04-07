<?php

namespace Habbo\Gamedata\Http\Controllers;

use Habbo\Gamedata\Services\ExternalOverrideTextsService;
use Habbo\Gamedata\Services\ExternalTextsService;
use Habbo\Gamedata\Services\ExternalVariablesService;
use Habbo\Gamedata\Services\FigureDataService;
use Habbo\Gamedata\Services\FurniDataService;
use Habbo\Gamedata\Services\ProductDataService;
use Illuminate\Http\JsonResponse;

class HashesController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'hashes' => [
                [
                    'name' => 'furnidata',
                    'url' => app(FurniDataService::class)->url(),
                    'hash' => app(FurniDataService::class)->hash(),
                ],
                [
                    'name' => 'productdata',
                    'url' => app(ProductDataService::class)->url(),
                    'hash' => app(ProductDataService::class)->hash(),
                ],
                [
                    'name' => 'external_variables',
                    'url' => app(ExternalVariablesService::class)->url(),
                    'hash' => app(ExternalVariablesService::class)->hash(),
                ],
                [
                    'name' => 'external_texts',
                    'url' => app(ExternalTextsService::class)->url(),
                    'hash' => app(ExternalTextsService::class)->hash(),
                ],
                [
                    'name' => 'external_override_texts',
                    'url' => app(ExternalOverrideTextsService::class)->url(),
                    'hash' => app(ExternalOverrideTextsService::class)->hash(),
                ],
                [
                    'name' => 'figurepartlist',
                    'url' => app(FigureDataService::class)->url(),
                    'hash' => app(FigureDataService::class)->hash(),
                ],
            ],
        ]);
    }
}
