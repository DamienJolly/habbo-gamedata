<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalVariable extends Model
{
    protected $table = 'external_variables';

    protected $fillable = [
        'key',
        'value',
    ];
}
