<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FigureSet extends Model
{
    protected $table = 'figure_sets';

    protected $fillable = [
        'figure_set_type_id',
        'set_id',
        'gender',
        'club',
        'colorable',
        'selectable',
        'preselectable',
    ];

    protected $casts = [
        'colorable' => 'boolean',
        'selectable' => 'boolean',
        'preselectable' => 'boolean',
    ];

    public function setType(): BelongsTo
    {
        return $this->belongsTo(FigureSetType::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(FigurePart::class);
    }
}
