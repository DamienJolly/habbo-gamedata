<?php

namespace Habbo\Gamedata\Console\Commands;

use Habbo\Gamedata\Models\FigurePalette;
use Habbo\Gamedata\Models\FigurePaletteColor;
use Habbo\Gamedata\Models\FigurePart;
use Habbo\Gamedata\Models\FigureSet;
use Habbo\Gamedata\Models\FigureSetType;
use Habbo\Gamedata\Services\FigureDataService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('gamedata:import-figure-data {path? : Path to figuredata XML file}')]
#[Description('Import figure data from a figuredata.xml file')]
class ImportFigureData extends Command
{
    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/imports/figuredata.xml');

        if (! File::exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $xml = simplexml_load_file($path);

        if ($xml === false) {
            $this->error('Failed to parse XML.');

            return self::FAILURE;
        }

        $paletteCreated = 0;
        $paletteUpdated = 0;
        $colorCreated = 0;
        $colorUpdated = 0;
        $setTypeCreated = 0;
        $setTypeUpdated = 0;
        $setCreated = 0;
        $setUpdated = 0;
        $partCreated = 0;
        $partUpdated = 0;

        DB::transaction(function () use (
            $xml,
            &$paletteCreated,
            &$paletteUpdated,
            &$colorCreated,
            &$colorUpdated,
            &$setTypeCreated,
            &$setTypeUpdated,
            &$setCreated,
            &$setUpdated,
            &$partCreated,
            &$partUpdated
        ): void {
            foreach ($xml->colors->palette as $paletteXml) {
                $palette = FigurePalette::updateOrCreate(
                    ['palette_id' => (int) $paletteXml['id']],
                    []
                );

                if ($palette->wasRecentlyCreated) {
                    $paletteCreated++;
                } else {
                    $paletteUpdated++;
                }

                foreach ($paletteXml->color as $colorXml) {
                    $color = FigurePaletteColor::updateOrCreate(
                        [
                            'figure_palette_id' => $palette->id,
                            'color_id' => (int) $colorXml['id'],
                        ],
                        [
                            'index' => (int) $colorXml['index'],
                            'club' => (int) $colorXml['club'],
                            'selectable' => ((string) $colorXml['selectable']) === '1',
                            'hex_code' => strtoupper(trim((string) $colorXml)),
                        ]
                    );

                    if ($color->wasRecentlyCreated) {
                        $colorCreated++;
                    } else {
                        $colorUpdated++;
                    }
                }
            }

            foreach ($xml->sets->settype as $setTypeXml) {
                $setType = FigureSetType::updateOrCreate(
                    ['type' => (string) $setTypeXml['type']],
                    [
                        'palette_id' => (int) $setTypeXml['paletteid'],
                        'mand_m_0' => ((string) $setTypeXml['mand_m_0']) === '1',
                        'mand_f_0' => ((string) $setTypeXml['mand_f_0']) === '1',
                        'mand_m_1' => ((string) $setTypeXml['mand_m_1']) === '1',
                        'mand_f_1' => ((string) $setTypeXml['mand_f_1']) === '1',
                    ]
                );

                if ($setType->wasRecentlyCreated) {
                    $setTypeCreated++;
                } else {
                    $setTypeUpdated++;
                }

                foreach ($setTypeXml->set as $setXml) {
                    $set = FigureSet::updateOrCreate(
                        [
                            'figure_set_type_id' => $setType->id,
                            'set_id' => (int) $setXml['id'],
                        ],
                        [
                            'gender' => (string) $setXml['gender'],
                            'club' => (int) $setXml['club'],
                            'colorable' => ((string) $setXml['colorable']) === '1',
                            'selectable' => ((string) $setXml['selectable']) === '1',
                            'preselectable' => ((string) $setXml['preselectable']) === '1',
                        ]
                    );

                    if ($set->wasRecentlyCreated) {
                        $setCreated++;
                    } else {
                        $setUpdated++;
                    }

                    foreach ($setXml->part as $partXml) {
                        $part = FigurePart::updateOrCreate(
                            [
                                'figure_set_id' => $set->id,
                                'part_id' => (int) $partXml['id'],
                                'type' => (string) $partXml['type'],
                            ],
                            [
                                'colorable' => ((string) $partXml['colorable']) === '1',
                                'index' => (int) $partXml['index'],
                                'colorindex' => (int) $partXml['colorindex'],
                            ]
                        );

                        if ($part->wasRecentlyCreated) {
                            $partCreated++;
                        } else {
                            $partUpdated++;
                        }
                    }
                }
            }
        });

        app(FigureDataService::class)->clearCache();

        $this->info('Import completed.');
        $this->line("Path: {$path}");
        $this->line("Palettes - Created: {$paletteCreated}, Updated: {$paletteUpdated}");
        $this->line("Palette Colors - Created: {$colorCreated}, Updated: {$colorUpdated}");
        $this->line("Set Types - Created: {$setTypeCreated}, Updated: {$setTypeUpdated}");
        $this->line("Sets - Created: {$setCreated}, Updated: {$setUpdated}");
        $this->line("Parts - Created: {$partCreated}, Updated: {$partUpdated}");

        return self::SUCCESS;
    }
}
