<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;

class ProductData extends Model
{
    protected $table = 'product_data';

    protected $fillable = [
        'code',
        'name',
        'description',
    ];
}
