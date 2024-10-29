<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationChangeLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'configuration_id',
        'from',
        'to',
    ];

    public $timestamps = false;
}
