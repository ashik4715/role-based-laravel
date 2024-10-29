<?php

namespace Database\Seeders;

use App\Services\Configuration\ConfigurationKeys;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configurations')->delete();
        DB::table('configurations')->insert(
[            
            [
                'key' => ConfigurationKeys::RESUBMIT_LIMIT,
                'value' => '3',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'key' => ConfigurationKeys::RANDOM_QC_PASS_PERCENT,
                'value' => '10',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'key' => ConfigurationKeys::RANDOM_QC_FAIL_PERCENT,
                'value' => '10',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'key' => ConfigurationKeys::MACHINE_QC_THRESHOLD,
                'value' => '50',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'key' => ConfigurationKeys::APPLICATION_MAX_OPERATION_TIME,
                'value' => '3',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'key' => ConfigurationKeys::DRAFT_FORM_LIFETIME,
                'value' => '3',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'key' => ConfigurationKeys::TERMS_AND_CONDITIONS,
                'value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda commodi consequatur cumque cupiditate, deleniti dolorem doloremque dolores earum, eligendi est harum maxime modi pariatur provident quasi quibusdam rem sunt veritatis.',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'key' => ConfigurationKeys::SINGLE_NID_APPLICATION_LIMIT,
                'value' => '3',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ]
        );
    }
}
