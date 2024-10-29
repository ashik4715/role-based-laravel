<?php

namespace Database\DataMigrations;

use App\Models\Field;
use App\Services\Application\Form\ValidationRules\Rule;
use Polygontech\DataMigration\Contracts\DataMigrationInterface;

class UpdateFarmerCardValidation implements DataMigrationInterface
{
    /**
     * Run the migration.
     *
     * @return null | string
     */
    public function handle()
    {
        Field::where('slug', 'farmer_card_image')->update([
            'rules' => (new Rule(
                isRequired: false
            ))->toJson(),
        ]);
    }
}
