<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FigurePart extends Model
{
    protected $table = 'figure_parts';

    protected $fillable = [
        'figure_set_id',
        'part_id',
        'type',
        'colorable',
        'index',
        'colorindex',
    ];

    protected $casts = [
        'colorable' => 'boolean',
    ];

    public function figureSet(): BelongsTo
    {
        return $this->belongsTo(FigureSet::class);
    }
}
