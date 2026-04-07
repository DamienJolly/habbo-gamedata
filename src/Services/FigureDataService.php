<?php

namespace Habbo\Gamedata\Services;

use Habbo\Gamedata\Concerns\HasGameDataEndpoint;
use Habbo\Gamedata\Concerns\WritesGameDataXml;
use Habbo\Gamedata\Models\FigurePart;
use Habbo\Gamedata\Models\FigurePalette;
use Habbo\Gamedata\Models\FigurePaletteColor;
use Habbo\Gamedata\Models\FigureSet;
use Habbo\Gamedata\Models\FigureSetType;
use Habbo\Gamedata\Support\GameDataConfig;
use Illuminate\Support\Facades\Cache;

class FigureDataService
{
    use HasGameDataEndpoint;
    use WritesGameDataXml;

    protected string $cacheKey;
    protected string $path;

    public function __construct()
    {
        $this->cacheKey = GameDataConfig::cacheKey('figure_data');
        $this->path = GameDataConfig::path('figure_data');
    }

    public function content(): string
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return $this->withWriter(function () {
                $this->startElement('figuredata');

                $this->startElement('colors');
                foreach (FigurePalette::query()->orderBy('palette_id')->cursor() as $palette) {
                    $this->startElement('palette');
                    $this->writeAttribute('id', $palette->palette_id);

                    $colors = FigurePaletteColor::query()
                        ->where('figure_palette_id', $palette->id)
                        ->orderBy('index')
                        ->orderBy('color_id')
                        ->cursor();

                    foreach ($colors as $color) {
                        $this->startElement('color');
                        $this->writeAttribute('id', $color->color_id);
                        $this->writeAttribute('index', $color->index);
                        $this->writeAttribute('club', $color->club);
                        $this->writeAttribute('selectable', $this->booleanString($color->selectable));
                        $this->writeText(strtoupper($color->hex_code));
                        $this->endElement();
                    }

                    $this->endElement();
                }
                $this->endElement();

                $this->startElement('sets');
                foreach (FigureSetType::query()->orderBy('type')->cursor() as $setType) {
                    $this->startElement('settype');
                    $this->writeAttribute('type', $setType->type);
                    $this->writeAttribute('paletteid', $setType->palette_id);
                    $this->writeAttribute('mand_m_0', $this->booleanString($setType->mand_m_0));
                    $this->writeAttribute('mand_f_0', $this->booleanString($setType->mand_f_0));
                    $this->writeAttribute('mand_m_1', $this->booleanString($setType->mand_m_1));
                    $this->writeAttribute('mand_f_1', $this->booleanString($setType->mand_f_1));

                    $sets = FigureSet::query()
                        ->where('figure_set_type_id', $setType->id)
                        ->orderBy('set_id')
                        ->cursor();

                    foreach ($sets as $set) {
                        $this->startElement('set');
                        $this->writeAttribute('id', $set->set_id);
                        $this->writeAttribute('gender', $set->gender);
                        $this->writeAttribute('club', $set->club);
                        $this->writeAttribute('colorable', $this->booleanString($set->colorable));
                        $this->writeAttribute('selectable', $this->booleanString($set->selectable));
                        $this->writeAttribute('preselectable', $this->booleanString($set->preselectable));

                        $parts = FigurePart::query()
                            ->where('figure_set_id', $set->id)
                            ->orderBy('index')
                            ->orderBy('part_id')
                            ->cursor();

                        foreach ($parts as $part) {
                            $this->startElement('part');
                            $this->writeAttribute('id', $part->part_id);
                            $this->writeAttribute('type', $part->type);
                            $this->writeAttribute('colorable', $this->booleanString($part->colorable));
                            $this->writeAttribute('index', $part->index);
                            $this->writeAttribute('colorindex', $part->colorindex);
                            $this->endElement();
                        }

                        $this->endElement();
                    }

                    $this->endElement();
                }
                $this->endElement();

                $this->endElement();
            });
        });
    }

    protected function clearDependentCaches(): void
    {
        app(ExternalVariablesService::class)->clearCache();
    }
}
