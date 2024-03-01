<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(){
        
        $roleId = Auth::user()->role_id;

        if(! $roleId) return redirect()->route('login');

        if($roleId === UserRole::ADMIN){
            return view('dashboard.admin');
        }
        else if($roleId === UserRole::TEACHER){
            return view('dashboard.teacher');
        }
        else if($roleId === UserRole::STUDENT){
            return view('dashboard.student');
        }

    }
}
