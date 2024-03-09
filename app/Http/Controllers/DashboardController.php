<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Enums\UserRole;
use App\Models\Absence;
use App\Models\Grade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request){
        
        $roleId = Auth::user()->role_id;

        switch ($roleId) {

            case UserRole::ADMIN: //IF ADMIN
    
                $currentMonth = Carbon::now()->month();
                $previousMonth = Carbon::now()->subMonth();

                // ---- Charges ----
                $chargesCollection = Charge::whereMonth(Charge::CREATED_AT, $currentMonth)
                ->orWhereMonth(Charge::CREATED_AT, $previousMonth)
                ->get();

                $currentMonthCharges = $chargesCollection->where(Charge::CREATED_AT, '>=', now()->month())->sum(fn($charge)=> $charge->price * $charge->quantity);
                $previousMonthCharges = $chargesCollection->where(Charge::CREATED_AT, '<', now()->month())->sum(fn($charge)=> $charge->price * $charge->quantity);

                $totalCharges = (object) [
                    'currentMonth' => number_format($currentMonthCharges),
                    'variation_rate' =>  number_format($currentMonthCharges / $previousMonthCharges * 100 - 100, 2) ,
                ];

                // ---- Student Grades ----
                $studentGradesCollection = Grade::whereMonth(Grade::CREATED_AT, $currentMonth)
                ->orWhereMonth(Grade::CREATED_AT, $previousMonth)
                ->get([Grade::CREATED_AT, Grade::GRADE_COLUMN_NAME]);
                
                $currentMonthAvgStudentGrade = $studentGradesCollection->where(Grade::CREATED_AT, '>=', $currentMonth)->avg(Grade::GRADE_COLUMN_NAME);
                $previousMonthAvgStudentGrade = $studentGradesCollection->where(Grade::CREATED_AT, '<', $previousMonth)->avg(Grade::GRADE_COLUMN_NAME);

                $avgStudentGrade = (object) [
                    'currentMonth' => number_format($currentMonthAvgStudentGrade, 2),
                    'variation_rate' => number_format($currentMonthAvgStudentGrade / $previousMonthAvgStudentGrade * 100 - 100, 2),
                ];

                // ---- Teachers ----
                $teacherCollection = User::teachers()->get([User::CREATED_AT]);
                $currentTeachers = $teacherCollection->count();
                $previousYearTeachers = $teacherCollection->where(User::CREATED_AT, '<=', Carbon::now()->subYear())->count();

                $teachersCount = (object) [
                    'currentYear' => $currentTeachers,
                    'variation' => $currentTeachers - $previousYearTeachers,
                ];
                
                // ---- Students ----
                $studentCollection = User::students()->get([User::CREATED_AT, User::GENDER_COLUMN_NAME]);

                $currentStudents = $studentCollection->count();
                $previousYearStudents = $studentCollection->where(User::CREATED_AT, '<=', Carbon::now()->subYear())->count();
                $maleStudents = $studentCollection->where(User::GENDER_COLUMN_NAME, User::GENDER_MALE)->count();
                $femaleStudents = $studentCollection->where(User::GENDER_COLUMN_NAME, User::GENDER_FEMALE)->count();
                
                $studentsCount = (object) [
                    'currentYear' => $currentStudents,
                    'variation' => $currentStudents - $previousYearStudents,
                    'boys' => $maleStudents,
                    'girls' => $femaleStudents,
                ];

                // ---- Students Absence ----
                $lastWeekAbsencesCollection = Absence::whereBetween(Absence::FROM_COLUMN_NAME, [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()->subDay(),
                ])->get([Absence::FROM_COLUMN_NAME, Absence::TO_COLUMN_NAME]);

                
                // Group the absences by the day of the week
                $lastWeekAbsences = [];
                foreach ($lastWeekAbsencesCollection as $absence) {
                    $dayOfWeek = $absence->from->dayOfWeek; // 1 (Monday) to 6 (Saturday)
                    if (!isset($lastWeekAbsences[$dayOfWeek])) {
                        $lastWeekAbsences[$dayOfWeek] = 0;
                    }
                    $lastWeekAbsences[$dayOfWeek] += $absence->to->hour - $absence->from->hour;
                }
                
                $viewData = compact('totalCharges', 'avgStudentGrade', 'teachersCount', 'studentsCount', 'lastWeekAbsences');
                
                return view('dashboard.admin', $viewData);
                break;

            case UserRole::TEACHER: // IF TEACHER

                return view('dashboard.teacher');
                break;

            case UserRole::STUDENT: // IF STUDENT

                return view('dashboard.student');
                break;

            default:
                return redirect()->route('login');
                break;
        }
    }
}
