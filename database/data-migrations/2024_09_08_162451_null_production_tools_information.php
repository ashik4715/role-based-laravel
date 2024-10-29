<?php

namespace Database\DataMigrations;

use App\Models\Section;
use Polygontech\DataMigration\Contracts\DataMigrationInterface;

class NullProductionToolsInformation implements DataMigrationInterface
{
    /**
     * Run the migration.
     *
     * @return null | string
     */
    public function handle()
    {
        // $order = Section::where('slug', 'production_information')->value('order') ?? 9;
        // Section::where('slug', 'production_tools_information')->delete();

        // $order = $order + 1;
        // Section::where('slug', 'stack_holder_information')->update([
        //     'order' => $order,
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'previous_experience')->update([
        //     'order' => $order,
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'review')->update([
        //     'order' => $order,
        // ]);
    }
}
