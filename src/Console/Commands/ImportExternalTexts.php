<?php

namespace Habbo\Gamedata\Console\Commands;

use Habbo\Gamedata\Models\ExternalText;
use Habbo\Gamedata\Services\ExternalTextsService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('gamedata:import-external-texts {path? : Path to key=value file}')]
#[Description('Import external flash texts from a key=value file')]
class ImportExternalTexts extends Command
{
    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/imports/external_flash_texts.txt');

        if (! File::exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $processed = 0;
        $created = 0;
        $updated = 0;
        $skippedEmpty = 0;
        $skippedInvalid = 0;

        foreach (File::lines($path) as $line) {
            $line = trim($line);

            if ($line === '') {
                $skippedEmpty++;

                continue;
            }

            if (! str_contains($line, '=')) {
                $skippedInvalid++;

                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);

            if ($key === '') {
                $skippedInvalid++;

                continue;
            }

            $externalText = ExternalText::updateOrCreate(
                ['key' => $key],
                ['value' => trim($value)]
            );

            if ($externalText->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            $processed++;
        }

        app(ExternalTextsService::class)->clearCache();

        $this->info('Import completed.');
        $this->line("Path: {$path}");
        $this->line("Processed: {$processed}");
        $this->line("Created: {$created}");
        $this->line("Updated: {$updated}");
        $this->line("Skipped (empty): {$skippedEmpty}");
        $this->line("Skipped (invalid): {$skippedInvalid}");

        return self::SUCCESS;
    }
}
