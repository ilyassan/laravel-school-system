<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Services\DashboardDataService;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardDataService $service){
        
        $roleId = $request->user()->role_id;

        switch ($roleId) {

            case UserRole::ADMIN: //IF ADMIN

                $viewData = $service->adminDashboardData();
                return view('dashboard.admin', $viewData);

            case UserRole::TEACHER: // IF TEACHER

                $viewData = $service->teacherDashboardData();
                return view('dashboard.teacher', $viewData);

            case UserRole::STUDENT: // IF STUDENT

                return view('dashboard.student');

            default:
                return redirect()->route('login');
        }
    }
}
