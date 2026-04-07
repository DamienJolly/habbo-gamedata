<?php

namespace Habbo\Gamedata\Console\Commands;

use Habbo\Gamedata\Models\FurniData;
use Habbo\Gamedata\Models\FurniDataPartColor;
use Habbo\Gamedata\Services\FurniDataService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('gamedata:import-furni-data {path? : Path to furnidata XML file}')]
#[Description('Import furni data from a furnidata.xml file')]
class ImportFurniData extends Command
{
    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/imports/furnidata.xml');

        if (! File::exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $xml = simplexml_load_file($path);

        if ($xml === false) {
            $this->error('Failed to parse XML.');

            return self::FAILURE;
        }

        $nullableText = function ($node): ?string {
            if (! isset($node)) {
                return null;
            }

            $value = trim((string) $node);

            return $value === '' ? null : $value;
        };

        $boolNode = function ($node): bool {
            return ((string) $node) === '1';
        };

        $intNode = function ($node, int $default = 0): int {
            if (! isset($node)) {
                return $default;
            }

            $value = trim((string) $node);

            return $value === '' ? $default : (int) $value;
        };

        $roomCreated = 0;
        $roomUpdated = 0;
        $wallCreated = 0;
        $wallUpdated = 0;
        $partColorsCreated = 0;
        $partColorsDeleted = 0;

        DB::transaction(function () use (
            $xml,
            $nullableText,
            $boolNode,
            $intNode,
            &$roomCreated,
            &$roomUpdated,
            &$wallCreated,
            &$wallUpdated,
            &$partColorsCreated,
            &$partColorsDeleted
        ): void {
            foreach ($xml->roomitemtypes->furnitype as $itemXml) {
                $item = FurniData::updateOrCreate(
                    [
                        'item_type' => 's',
                        'sprite_id' => (int) $itemXml['id'],
                    ],
                    [
                        'item_name' => (string) $itemXml['classname'],
                        'revision' => $intNode($itemXml->revision),
                        'category' => $nullableText($itemXml->category),
                        'default_direction' => $intNode($itemXml->defaultdir),
                        'length' => $intNode($itemXml->xdim),
                        'width' => $intNode($itemXml->ydim),
                        'name' => $nullableText($itemXml->name),
                        'description' => $nullableText($itemXml->description),
                        'ad_url' => $nullableText($itemXml->adurl),
                        'offer_id' => $intNode($itemXml->offerid, -1),
                        'buyout' => $boolNode($itemXml->buyout),
                        'rent_offer_id' => $intNode($itemXml->rentofferid, -1),
                        'rent_buyout' => $boolNode($itemXml->rentbuyout),
                        'bc' => $boolNode($itemXml->bc),
                        'excluded_dynamic' => $intNode($itemXml->excludeddynamic),
                        'bc_offer_id' => $intNode($itemXml->bcofferid, -1),
                        'custom_params' => $nullableText($itemXml->customparams),
                        'special_type' => $intNode($itemXml->specialtype),
                        'can_stand_on' => $boolNode($itemXml->canstandon),
                        'can_sit_on' => $boolNode($itemXml->cansiton),
                        'can_lay_on' => $boolNode($itemXml->canlayon),
                        'furni_line' => $nullableText($itemXml->furniline),
                        'environment' => $nullableText($itemXml->environment),
                        'rare' => $boolNode($itemXml->rare),
                        'tradeable' => $boolNode($itemXml->tradeable),
                    ]
                );

                if ($item->wasRecentlyCreated) {
                    $roomCreated++;
                } else {
                    $roomUpdated++;
                }

                $partColorsDeleted += FurniDataPartColor::query()->where('furni_data_id', $item->id)->delete();

                $index = 0;

                if (isset($itemXml->partcolors)) {
                    foreach ($itemXml->partcolors->color as $colorXml) {
                        FurniDataPartColor::query()->create([
                            'furni_data_id' => $item->id,
                            'color_hex' => trim((string) $colorXml),
                            'sort_order' => $index++,
                        ]);

                        $partColorsCreated++;
                    }
                }
            }

            foreach ($xml->wallitemtypes->furnitype as $itemXml) {
                $item = FurniData::updateOrCreate(
                    [
                        'item_type' => 'i',
                        'sprite_id' => (int) $itemXml['id'],
                    ],
                    [
                        'item_name' => (string) $itemXml['classname'],
                        'revision' => $intNode($itemXml->revision),
                        'category' => $nullableText($itemXml->category),
                        'default_direction' => null,
                        'length' => null,
                        'width' => null,
                        'name' => $nullableText($itemXml->name),
                        'description' => $nullableText($itemXml->description),
                        'ad_url' => $nullableText($itemXml->adurl),
                        'offer_id' => $intNode($itemXml->offerid, -1),
                        'buyout' => $boolNode($itemXml->buyout),
                        'rent_offer_id' => $intNode($itemXml->rentofferid, -1),
                        'rent_buyout' => $boolNode($itemXml->rentbuyout),
                        'bc' => $boolNode($itemXml->bc),
                        'excluded_dynamic' => $intNode($itemXml->excludeddynamic),
                        'bc_offer_id' => $intNode($itemXml->bcofferid, -1),
                        'custom_params' => null,
                        'special_type' => $intNode($itemXml->specialtype),
                        'can_stand_on' => null,
                        'can_sit_on' => null,
                        'can_lay_on' => null,
                        'furni_line' => $nullableText($itemXml->furniline),
                        'environment' => $nullableText($itemXml->environment),
                        'rare' => $boolNode($itemXml->rare),
                        'tradeable' => $boolNode($itemXml->tradeable),
                    ]
                );

                if ($item->wasRecentlyCreated) {
                    $wallCreated++;
                } else {
                    $wallUpdated++;
                }

                $partColorsDeleted += FurniDataPartColor::query()->where('furni_data_id', $item->id)->delete();
            }
        });

        app(FurniDataService::class)->clearCache();

        $this->info('Import completed.');
        $this->line("Path: {$path}");
        $this->line("Room Items - Created: {$roomCreated}, Updated: {$roomUpdated}");
        $this->line("Wall Items - Created: {$wallCreated}, Updated: {$wallUpdated}");
        $this->line("Part Colors - Created: {$partColorsCreated}, Deleted: {$partColorsDeleted}");

        return self::SUCCESS;
    }
}
