<?php

namespace Habbo\Gamedata\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalOverrideText extends Model
{
    protected $table = 'external_override_texts';

    protected $fillable = [
        'key',
        'value',
    ];
}
