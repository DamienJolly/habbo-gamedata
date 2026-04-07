<?php

namespace Habbo\Gamedata\Console\Commands;

use Habbo\Gamedata\Models\ProductData;
use Habbo\Gamedata\Services\ProductDataService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('gamedata:import-product-data {path? : Path to productdata XML file}')]
#[Description('Import product data from a productdata.xml file')]
class ImportProductData extends Command
{
    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/imports/productdata.xml');

        if (! File::exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $xml = simplexml_load_file($path);

        if ($xml === false) {
            $this->error('Failed to parse XML.');

            return self::FAILURE;
        }

        $processed = 0;
        $created = 0;
        $updated = 0;
        $skippedMissingCode = 0;

        foreach ($xml->product as $product) {
            $code = isset($product['code']) ? trim((string) $product['code']) : null;

            if ($code === null || $code === '') {
                $skippedMissingCode++;

                continue;
            }

            $name = isset($product->name) ? trim((string) $product->name) : null;
            $description = isset($product->description) ? trim((string) $product->description) : null;

            $name = $name === '' ? null : $name;
            $description = $description === '' ? null : $description;

            $productData = ProductData::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => $description,
                ]
            );

            if ($productData->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            $processed++;
        }

        app(ProductDataService::class)->clearCache();

        $this->info('Import completed.');
        $this->line("Path: {$path}");
        $this->line("Processed: {$processed}");
        $this->line("Created: {$created}");
        $this->line("Updated: {$updated}");
        $this->line("Skipped (missing code): {$skippedMissingCode}");

        return self::SUCCESS;
    }
}
