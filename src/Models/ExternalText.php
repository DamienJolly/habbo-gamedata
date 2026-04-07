<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalText extends Model
{
    protected $table = 'external_texts';

    protected $fillable = [
        'key',
        'value',
    ];
}
