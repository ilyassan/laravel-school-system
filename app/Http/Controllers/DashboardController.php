<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grade;
use App\Models\Charge;
use App\Models\Report;
use App\Enums\UserRole;
use App\Models\Absence;
use App\Models\Classes;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request){
        
        $roleId = Auth::user()->role_id;

        switch ($roleId) {

            case UserRole::ADMIN: //IF ADMIN
    
                $currentMonth = now()->startOfMonth();
                $previousMonth = now()->subMonth()->startOfMonth();
                $threeMonthAgo = now()->startOfMonth()->subMonths(3);

                $latestChargesLimit = 5;
                $latestReportsLimit= 3;
                $topClassesLimit= 5;

                // ---- Ratings ----
                $ratingsCollection = Rating::all(Rating::RATING_COLUMN);

                $ratings = (object) [
                    'count' => number_format($ratingsCollection->count()),
                    'avg' => $ratingsCollection->avg(Rating::RATING_COLUMN)
                ];

                // ---- Charges ----
                $currentAndPreviousMonthChargesCollection = Charge::whereMonth(Charge::CREATED_AT, $currentMonth)
                                            ->orWhereMonth(Charge::CREATED_AT, $previousMonth)
                                            ->get([Charge::CREATED_AT, Charge::PRICE_COLUMN, Charge::QUANTITY_COLUMN]);
                
                $currentMonthCharges = $currentAndPreviousMonthChargesCollection->where(Charge::CREATED_AT, '>=', $currentMonth)->sum(fn($charge)=> $charge->price * $charge->quantity);
                $previousMonthCharges = $currentAndPreviousMonthChargesCollection->where(Charge::CREATED_AT, '<', $currentMonth)->sum(fn($charge)=> $charge->price * $charge->quantity);

                $totalCharges = (object) [
                    'currentMonth' => number_format($currentMonthCharges),
                    'variation_rate' =>  $previousMonthCharges !== 0 ? number_format($currentMonthCharges / $previousMonthCharges * 100 - 100, 2) : null,
                ];

                $latestCharges = Charge::latest()->limit($latestChargesLimit)->get();

                // ---- Student Grades ----
                $studentGradesCollection = Grade::whereMonth(Grade::CREATED_AT, $currentMonth)
                                                ->orWhereMonth(Grade::CREATED_AT, $previousMonth)
                                                ->get([Grade::CREATED_AT, Grade::GRADE_COLUMN]);
                
                $currentMonthAvgStudentGrade = $studentGradesCollection->where(Grade::CREATED_AT, '>=', $currentMonth)->avg(Grade::GRADE_COLUMN);
                $previousMonthAvgStudentGrade = $studentGradesCollection->where(Grade::CREATED_AT, '<', $currentMonth)->avg(Grade::GRADE_COLUMN);

                $avgStudentGrade = (object) [
                    'currentMonth' => number_format($currentMonthAvgStudentGrade, 2),
                    'variation_rate' => $previousMonthAvgStudentGrade !== 0 ? number_format($currentMonthAvgStudentGrade / $previousMonthAvgStudentGrade * 100 - 100, 2) : null,
                ];

                // ---- Teachers ----
                $teacherCollection = User::teachers()->get([User::CREATED_AT]);

                $currentTeachers = $teacherCollection->count();
                $previousYearTeachers = $teacherCollection->where(User::CREATED_AT, '<', now()->startOfYear())->count();

                $teachersCount = (object) [
                    'currentYear' => $currentTeachers,
                    'variation' => $currentTeachers - $previousYearTeachers,
                ];
                
                // ---- Students ----
                $studentCollection = User::students()->get([User::CREATED_AT, User::GENDER_COLUMN]);

                $currentStudents = $studentCollection->count();
                $previousYearStudents = $studentCollection->where(User::CREATED_AT, '<', now()->startOfYear())->count();
                $maleStudents = $studentCollection->where(User::GENDER_COLUMN, User::GENDER_MALE)->count();
                $femaleStudents = $studentCollection->where(User::GENDER_COLUMN, User::GENDER_FEMALE)->count();
                
                $studentsCount = (object) [
                    'currentYear' => $currentStudents,
                    'variation' => $currentStudents - $previousYearStudents,
                    'boys' => $maleStudents,
                    'girls' => $femaleStudents,
                ];

                // ---- Students Absence ----
                $lastWeekAbsencesCollection = Absence::whereBetween(Absence::FROM_COLUMN, [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()->subDay(),
                ])->get([Absence::FROM_COLUMN, Absence::TO_COLUMN]);

                // Group the absences by the day of the week
                $lastWeekAbsences = [];
                foreach ($lastWeekAbsencesCollection as $absence) {
                    $dayOfWeek = $absence->from->dayOfWeek; // 1 (Monday) to 6 (Saturday)
                    if (!isset($lastWeekAbsences[$dayOfWeek])) {
                        $lastWeekAbsences[$dayOfWeek] = 0;
                    }
                    $lastWeekAbsences[$dayOfWeek] += $absence->to->hour - $absence->from->hour;
                }

                // ---- Teachers Reports ----
                $latestTeacherReports = Report::teacherReports()
                                                ->orderBy('reports.created_at')
                                                ->limit($latestReportsLimit)
                                                // For getting only the first 80 char in the description
                                                ->selectRaw("LEFT(description, 80) AS shortDescription")
                                                ->withAggregate('user', User::NAME_COLUMN)
                                                ->get();

                // ---- Students Reports ----
                $latestStudentsReports = Report::studentReports()
                                                ->latest('reports.created_at')
                                                ->limit($latestReportsLimit)
                                                // For getting only the first 140 char in the description
                                                ->selectRaw("LEFT(description, 140) AS shortDescription")
                                                ->withAggregate('user', User::NAME_COLUMN)
                                                ->get();

                // ---- Top Classes ----
                $topClasses = Classes::withAvgGrades($threeMonthAgo)->orderByDesc(Classes::AVG_GRADES)->limit($topClassesLimit)->get();

                $viewData = compact('ratings', 'totalCharges', 'latestCharges', 'avgStudentGrade', 'teachersCount', 'studentsCount', 'lastWeekAbsences', 'latestTeacherReports', 'latestStudentsReports', 'topClasses');
                
                return view('dashboard.admin', $viewData);

            case UserRole::TEACHER: // IF TEACHER

                return view('dashboard.teacher');

            case UserRole::STUDENT: // IF STUDENT

                return view('dashboard.student');

            default:
                return redirect()->route('login');
        }
    }
}
