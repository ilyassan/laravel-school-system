<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends BaseController
{
    public function __invoke(Request $request, DashboardService $service)
    {

        $roleId = $this->getAuthUser()->getRoleId();

        switch ($roleId) {

            case UserRole::ADMIN: //IF ADMIN

                $viewData = $service->adminDashboardData();
                return view('dashboard.admin', $viewData);

            case UserRole::TEACHER: // IF TEACHER

                $viewData = $service->teacherDashboardData();
                return view('dashboard.teacher', $viewData);

            case UserRole::STUDENT: // IF STUDENT

                $viewData = $service->studentDashboardData();
                return view('dashboard.student', $viewData);

            default:
                return redirect()->route('login');
        }
    }
}
