<?php

namespace Database\DataMigrations;

use App\Models\Section;
use Illuminate\Support\Str;
use Polygontech\DataMigration\Contracts\DataMigrationInterface;

class RemoveGuarantorOtpStep implements DataMigrationInterface
{
    /**
     * Run the migration.
     *
     * @return null | string
     */
    public function handle()
    {
        Section::where('slug', 'guarantor-otp')->delete();
        $order = Section::where('slug', Str::slug('Guarantor'))->first()->order;
        // $order = $order + 1;
        // Section::where('slug', Str::slug('Nominee'))->update([
        //     'order' => $order
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'financial_information')->update([
        //     'order' => $order
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'lifestyle_information')->update([
        //     'order' => $order
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'professional_information')->update([
        //     'order' => $order
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'production_information')->update([
        //     'order' => $order
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'production_tools_information')->update([
        //     'order' => $order
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'stack_holder_information')->update([
        //     'order' => $order
        // ]);
        // $order = $order + 1;
        // Section::where('slug', 'previous_experience')->update([
        //     'order' => $order
        // ]);
        $order = $order + 1;
        Section::where('slug', 'assessment_info')->update([
            'order' => $order
        ]);
        $order = $order + 1;
        Section::where('slug', 'project_loan_details')->update([
            'order' => $order
        ]);
        $order = $order + 1;
        Section::where('slug', 'review')->update([
            'order' => $order
        ]);
    }
}
