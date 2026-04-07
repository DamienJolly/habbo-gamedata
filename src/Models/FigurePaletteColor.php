<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FigurePaletteColor extends Model
{
    protected $table = 'figure_palette_colors';

    protected $fillable = [
        'figure_palette_id',
        'color_id',
        'index',
        'club',
        'selectable',
        'hex_code',
    ];

    protected $casts = [
        'selectable' => 'boolean',
    ];

    public function palette(): BelongsTo
    {
        return $this->belongsTo(FigurePalette::class, 'figure_palette_id');
    }
}
