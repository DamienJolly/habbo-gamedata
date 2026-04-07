<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FurniData extends Model
{
    protected $table = 'furni_data';

    protected $fillable = [
        'sprite_id',
        'item_type',
        'item_name',
        'revision',
        'category',
        'default_direction',
        'length',
        'width',
        'name',
        'description',
        'ad_url',
        'offer_id',
        'buyout',
        'rent_offer_id',
        'rent_buyout',
        'bc',
        'excluded_dynamic',
        'bc_offer_id',
        'custom_params',
        'special_type',
        'can_stand_on',
        'can_sit_on',
        'can_lay_on',
        'furni_line',
        'environment',
        'rare',
        'tradeable',
    ];

    protected $casts = [
        'buyout' => 'boolean',
        'rent_buyout' => 'boolean',
        'bc' => 'boolean',
        'can_stand_on' => 'boolean',
        'can_sit_on' => 'boolean',
        'can_lay_on' => 'boolean',
        'rare' => 'boolean',
        'tradeable' => 'boolean',
    ];

    public function partColors(): HasMany
    {
        return $this->hasMany(FurniDataPartColor::class)->orderBy('sort_order');
    }
}
