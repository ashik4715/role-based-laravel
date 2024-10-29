<?php

namespace App\Models;

use App\Services\Configuration\ConfigurationKeys;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $value
 */
class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'key' => ConfigurationKeys::class,
    ];
}
