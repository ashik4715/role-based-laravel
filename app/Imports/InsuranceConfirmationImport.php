<?php
namespace App\Imports;

use App\Models\InsuranceConfirmation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InsuranceConfirmationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip the row if `nid`, `acceptance`, or `project_name` is null
        if (empty($row['nid']) || empty($row['acceptance']) || empty($row['project_name'])) {
            return null;  
        }

        $filteredRow = [
            'nid' => $row['nid'],
            'acceptance' => $row['acceptance'],
            'project_name' => $row['project_name'],
        ];
        // dd($filteredRow);
        return new InsuranceConfirmation($filteredRow);
    }
}