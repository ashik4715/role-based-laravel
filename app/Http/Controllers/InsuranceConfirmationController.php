<?php

namespace App\Http\Controllers;

use App\Imports\InsuranceConfirmationImport;
use App\Models\InsuranceConfirmation;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

class InsuranceConfirmationController extends Controller
{
    public function index()
    {
        $insuranceConfirmations = InsuranceConfirmation::all();

        // Check if the table is empty
        if ($insuranceConfirmations->isEmpty()) {
            $this->fetchAndStoreData();
            $insuranceConfirmations = InsuranceConfirmation::all(); // Re-fetch after storing
        }

        $acceptedCount = InsuranceConfirmation::where('acceptance', 'yes')->count();
        $rejectedCount = InsuranceConfirmation::where('acceptance', 'no')->count();

        return view('backend.pages.insurance_confirmations.index', [
            'total_insurance' => InsuranceConfirmation::count(),
            'accepted_count' => $acceptedCount,
            'rejected_count' => $rejectedCount,
        ], compact('insuranceConfirmations'));
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

        return redirect()->route('admin.insurance-confirmations.view')->with('success', 'Insurance confirmations imported successfully!');
    }

    public function fetchAndStoreData()
    {
        // Fetch data from the API
        $response = Http::withHeaders([
            'api-key' => 'paglaapi',
        ])->get('http://103.191.179.87:8099/data');

        if ($response->successful()) {
            // Decode JSON data
            $data = $response->json()['data'];
            foreach ($data as $item) {
                // Insert or update record in the insurance_confirmations table
                InsuranceConfirmation::updateOrCreate(
                    ['fid' => $item['fid']], // Unique identifier to avoid duplicates
                    [
                        'farmer_name' => $item['farmer_name'],
                        'nid' => $item['nid'],
                        'phone' => $item['phone'],
                        'thana' => $item['thana'],
                        'area' => $item['area'],
                        'region' => $item['region'],
                        'project_name' => $item['project_name'],
                        'fo_id' => $item['fo_id'],
                        'fo_name' => $item['fo_name'],
                        'area_manager' => $item['area_manager'],
                        'regional_manager' => $item['regional_manager'],
                        'approved_amount' => $item['approved_amount'],
                        'acceptance' => $item['acceptance'] ?? null,
                    ]
                );
            }

            return response()->json(['message' => 'Data fetched and stored successfully']);
        } else {
            return response()->json(['error' => 'Failed to fetch data from API'], 500);
        }
    }

    public function updateAcceptance(Request $request, $id)
    {
        $confirmation = InsuranceConfirmation::findOrFail($id);

        // Validate the selected value for acceptance
        $request->validate([
            'acceptance' => 'required|in:yes,no',
        ]);

        // Update the acceptance field
        $confirmation->update([
            'acceptance' => $request->input('acceptance'),
        ]);
        $acceptanceStatus = $request->input('acceptance');
        $message = '';
        // success or error
        $messageType = ''; 

        if ($acceptanceStatus === 'yes') {
            $message = "Approval of {$confirmation->farmer_name} is Approved!";
            $messageType = 'success';
        } else {
            $message = "Approval of {$confirmation->farmer_name} is Rejected!";
            $messageType = 'error';
        }

        return redirect()->route('admin.insurance-confirmations.view')
            ->with($messageType, $message);
    }

}
