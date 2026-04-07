<?php

use Habbo\Gamedata\Http\Controllers\ExternalOverrideTextsController;
use Habbo\Gamedata\Http\Controllers\ExternalTextsController;
use Habbo\Gamedata\Http\Controllers\ExternalVariablesController;
use Habbo\Gamedata\Http\Controllers\FigureDataController;
use Habbo\Gamedata\Http\Controllers\FurniDataController;
use Habbo\Gamedata\Http\Controllers\HashesController;
use Habbo\Gamedata\Http\Controllers\ProductDataController;
use Habbo\Gamedata\Support\GameDataConfig;
use Illuminate\Support\Facades\Route;

Route::get(GameDataConfig::routePath('hashes'), [HashesController::class, 'index']);

Route::get(GameDataConfig::routePath('external_texts') . '/{hash?}', [ExternalTextsController::class, 'show'])
    ->where('hash', '.*');

Route::get(GameDataConfig::routePath('product_data') . '/{hash?}', [ProductDataController::class, 'show'])
    ->where('hash', '.*');

Route::get(GameDataConfig::routePath('figure_data') . '/{hash?}', [FigureDataController::class, 'show'])
    ->where('hash', '.*');

Route::get(GameDataConfig::routePath('furni_data') . '/{hash?}', [FurniDataController::class, 'show'])
    ->where('hash', '.*');

Route::get(GameDataConfig::routePath('external_override_texts') . '/{hash?}', [ExternalOverrideTextsController::class, 'show'])
    ->where('hash', '.*');
