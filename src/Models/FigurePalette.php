<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FigurePalette extends Model
{
    protected $table = 'figure_palettes';

    protected $fillable = [
        'palette_id',
    ];

    public function colors(): HasMany
    {
        return $this->hasMany(FigurePaletteColor::class);
    }
}
