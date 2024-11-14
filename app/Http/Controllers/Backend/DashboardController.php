<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Application;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['dashboard.view']);

        $response = Http::withHeaders([
            'api-key' => 'wegro_control_service',
        ])->get('http://192.168.0.167:7002/api/wcp-applications');

        if ($response->successful()) {
            $data = $response->json();
        }

        $applications = Application::select(
            'id',
            'agent_id',
            'column_1',
            'application_data',
            'status',
            'address',
            'created_at',
            'updated_at'
        )
        ->paginate(10);

        // Transform the collection for view usage
        $applications->getCollection()->transform(function ($application) 
        {

            $addressData = $application->address ? json_decode($application->address, true) : null;
            return [
                'id' => $application->id,
                'agent_id' => $application->agent_id,
                'phone' => $application->column_1,
                'data' => json_decode($application->application_data, true),
                'status' => $application->status,
                'address' => $addressData['address'] ?? null,
            ];
        });
        // dd($data, $applications[0]);

        return view(
            'backend.pages.dashboard.index',
            [
                'total_admins' => Admin::count(),
                'total_roles' => Role::count(),
                'total_permissions' => Permission::count(),
            ],
            compact('applications','data')
        );
    }

    public function dashboard(Request $request)
    {

    }
}
