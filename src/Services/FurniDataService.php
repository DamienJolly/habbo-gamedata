<?php

namespace Habbo\Gamedata\Services;

use Habbo\Gamedata\Concerns\HasGameDataEndpoint;
use Habbo\Gamedata\Concerns\WritesGameDataXml;
use Habbo\Gamedata\Models\FurniData;
use Habbo\Gamedata\Models\FurniDataPartColor;
use Habbo\Gamedata\Support\GameDataConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FurniDataService
{
    use HasGameDataEndpoint;
    use WritesGameDataXml;

    protected string $cacheKey;
    protected string $path;

    public function __construct()
    {
        $this->cacheKey = GameDataConfig::cacheKey('furni_data');
        $this->path = GameDataConfig::path('furni_data');
    }

    public function content(): string
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return $this->withWriter(function () {
                $this->startElement('furnidata');

                $this->startElement('roomitemtypes');
                $this->writeFurnitypesForItemType('s', true);
                $this->endElement();

                $this->startElement('wallitemtypes');
                $this->writeFurnitypesForItemType('i', false);
                $this->endElement();

                $this->endElement();
            });
        });
    }

    private function writeFurnitypesForItemType(string $itemType, bool $isRoomItem): void
    {
        FurniData::query()
            ->where('item_type', $itemType)
            ->orderBy('sprite_id')
            ->orderBy('id')
            ->chunk(500, function (Collection $items) use ($isRoomItem): void {
                $colorsByFurniId = collect();

                if ($isRoomItem && $items->isNotEmpty()) {
                    $colorsByFurniId = FurniDataPartColor::query()
                        ->whereIn('furni_data_id', $items->pluck('id')->all())
                        ->orderBy('furni_data_id')
                        ->orderBy('sort_order')
                        ->get()
                        ->groupBy('furni_data_id');
                }

                foreach ($items as $item) {
                    $this->writeFurnitype(
                        $item,
                        $isRoomItem,
                        $colorsByFurniId->get($item->id, collect())
                    );
                }
            });
    }

    private function writeFurnitype(FurniData $item, bool $isRoomItem, Collection $partColors): void
    {
        $this->startElement('furnitype');
        $this->writeAttribute('id', $item->sprite_id);
        $this->writeAttribute('classname', $item->item_name);

        $this->writeScalarElement('revision', $item->revision);
        $this->writeNullableElement('category', $item->category);

        if ($isRoomItem) {
            $this->writeScalarElement('defaultdir', $item->default_direction ?? 0);
            $this->writeScalarElement('xdim', $item->length ?? 0);
            $this->writeScalarElement('ydim', $item->width ?? 0);
            $this->writePartColors($partColors);
        }

        $this->writeNullableElement('name', $item->name);
        $this->writeNullableElement('description', $item->description);
        $this->writeNullableElement('adurl', $item->ad_url);
        $this->writeScalarElement('offerid', $item->offer_id ?? -1);
        $this->writeBooleanElement('buyout', $item->buyout);
        $this->writeScalarElement('rentofferid', $item->rent_offer_id ?? -1);
        $this->writeBooleanElement('rentbuyout', $item->rent_buyout);
        $this->writeBooleanElement('bc', $item->bc);
        $this->writeScalarElement('excludeddynamic', $item->excluded_dynamic ?? 0);
        $this->writeScalarElement('bcofferid', $item->bc_offer_id ?? -1);

        if ($isRoomItem) {
            $this->writeNullableElement('customparams', $item->custom_params);
        }

        $this->writeScalarElement('specialtype', $item->special_type ?? 0);

        if ($isRoomItem) {
            $this->writeBooleanElement('canstandon', $item->can_stand_on ?? false);
            $this->writeBooleanElement('cansiton', $item->can_sit_on ?? false);
            $this->writeBooleanElement('canlayon', $item->can_lay_on ?? false);
        }

        $this->writeNullableElement('furniline', $item->furni_line);
        $this->writeNullableElement('environment', $item->environment);
        $this->writeBooleanElement('rare', $item->rare);
        $this->writeBooleanElement('tradeable', $item->tradeable);

        $this->endElement();
    }

    private function writePartColors(Collection $partColors): void
    {
        if ($partColors->isEmpty()) {
            return;
        }

        $this->startElement('partcolors');

        foreach ($partColors as $color) {
            $this->writeScalarElement('color', $color->color_hex);
        }

        $this->endElement();
    }

    protected function clearDependentCaches(): void
    {
        app(ExternalVariablesService::class)->clearCache();
    }
}
