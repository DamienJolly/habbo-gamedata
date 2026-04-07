<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FigureSetType extends Model
{
    protected $table = 'figure_set_types';

    protected $fillable = [
        'type',
        'palette_id',
        'mand_m_0',
        'mand_f_0',
        'mand_m_1',
        'mand_f_1',
    ];

    protected $casts = [
        'mand_m_0' => 'boolean',
        'mand_f_0' => 'boolean',
        'mand_m_1' => 'boolean',
        'mand_f_1' => 'boolean',
    ];

    public function sets(): HasMany
    {
        return $this->hasMany(FigureSet::class);
    }
}
