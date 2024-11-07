<?php

namespace App\Http\Controllers;

use App\Imports\InsuranceConfirmationImport;
use App\Models\InsuranceConfirmation;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InsuranceConfirmationController extends Controller
{
    public function index()
    {
        $insuranceConfirmations = InsuranceConfirmation::all();
        return view('backend.pages.insurance_confirmations.index', compact('insuranceConfirmations'));
    }
    
    public function importView()
    {
        return view('backend.pages.insurance_confirmations.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        Excel::import(new InsuranceConfirmationImport, $request->file('file'));

        return redirect()->route('admin.insurance-confirmations.index')->with('success', 'Insurance confirmations imported successfully!');
    }
}
