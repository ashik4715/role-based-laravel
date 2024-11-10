<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceConfirmation extends Model
{
    use HasFactory;

    protected $table = 'insurance_confirmations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fid',
        'farmer_name',
        'nid',
        'phone',
        'thana',
        'area',
        'region',
        'project_name',
        'fo_id',
        'fo_name',
        'area_manager',
        'regional_manager',
        'approved_amount',
        'acceptance',
    ];

}
