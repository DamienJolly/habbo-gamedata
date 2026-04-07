<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FurniDataPartColor extends Model
{
    protected $table = 'furni_data_part_colors';

    protected $fillable = [
        'furni_data_id',
        'color_hex',
        'sort_order',
    ];

    public function furniData(): BelongsTo
    {
        return $this->belongsTo(FurniData::class);
    }
}
