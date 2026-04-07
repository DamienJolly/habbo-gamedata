<?php

namespace Habbo\Gamedata\Console\Commands;

use Habbo\Gamedata\Models\ExternalVariable;
use Habbo\Gamedata\Services\ExternalVariablesService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('gamedata:import-external-variables {path? : Path to key=value file}')]
#[Description('Import external variables from a key=value file')]
class ImportExternalVariables extends Command
{
    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/imports/external_variables.txt');

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

            $externalVariable = ExternalVariable::updateOrCreate(
                ['key' => $key],
                ['value' => trim($value)]
            );

            if ($externalVariable->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            $processed++;
        }

        app(ExternalVariablesService::class)->clearCache();

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
