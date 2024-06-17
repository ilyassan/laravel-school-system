<?php

namespace App\Services;

use Carbon\Carbon;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;

use App\Models\{User, Grade, Charge, Report, Absence, Classes, Homework, Rating};

class DashboardDataService
{
    private $lastWeek;
    private $currentMonth;
    private $previousMonth;
    private $threeMonthAgo;
    private $currentYear;

    public function __construct()
    {
        $this->lastWeek = now()->subWeek()->startOfWeek();
        $this->currentMonth = now()->startOfMonth();
        $this->previousMonth = now()->startOfMonth()->subMonth();
        $this->threeMonthAgo = now()->startOfMonth()->subMonths(3);
        $this->currentYear = now()->startOfYear();
    }

    public function adminDashboardData()
    {
        $latestChargesLimit = 5;
        $latestReportsLimit= 3;
        $topClassesLimit= 5;
        $teacherReportsDescriptionChars= 80;
        $studentReportsDescriptionChars= 140;

        return [
            'ratings' => $this->getRatings(),
            'charges' => $this->getCharges($this->currentMonth, $this->previousMonth),
            'latestCharges' => $this->getLatestCharges($latestChargesLimit),
            'avgStudentGrade' => $this->getAvgStudentsGrades($this->currentMonth, $this->previousMonth),
            'teachers' => $this->getTeachers($this->currentYear),
            'students' => $this->getStudents($this->currentYear),
            'lastWeekAbsences' => $this->getAbsences($this->lastWeek),
            'latestTeacherReports' => $this->getLatestReports(UserRole::TEACHER, $latestReportsLimit, $teacherReportsDescriptionChars),
            'latestStudentsReports' => $this->getLatestReports(UserRole::STUDENT, $latestReportsLimit, $studentReportsDescriptionChars),
            'topClasses' => $this->getTopClasses($topClassesLimit)
        ];
    }

    public function teacherDashboardData()
    {
        $topStudentsLimit = 5;
        $latestHomeworksLimit = 3;
        
        $teacherId = auth()->id();
        $teacherClasses = auth()->user()->classes()->pluck('id');

        return [
            'ratings' => $this->getRatings(),
            'teacherClasses' => $this->getTeacherClasses($this->currentYear),
            'teacherStudentsAvgGrades' => $this->getAvgStudentsGrades($this->currentMonth, $this->previousMonth, $teacherId),
            'teacherStudents' => $this->getTeacherStudents($this->currentYear, $teacherClasses),
            'salary' => auth()->user()->salary,
            'teacherClassesWithAbsences' => $this->getClassesWithAbsences($this->lastWeek, $teacherClasses),
            'topTeacherStudents' => $this->getTopStudents($teacherClasses, $topStudentsLimit),
            'latestHomeworks' => $this->getTeacherHomeworks($teacherId, $latestHomeworksLimit),
        ];
    }

    public function studentDashboardData()
    {
        $latestGradesLimit = 5;
        $topStudentsLimit = 5;
        $classHomeworksLimit = 5;

        $studentId = auth()->id();
        $classId = auth()->user()->class_id;

        $startOfYear = $this->currentYear;
        $endOfYear = $this->currentYear->clone()->endOfYear();

        return [
            'ratings' => $this->getRatings(),
            'avgStudentGradesEachMonth'  => $this->getAvgGradesOfEachMonth($startOfYear, $endOfYear, $studentId),
            'avgClassGradesEachMonth'  => $this->getAvgGradesOfEachMonth($startOfYear, $endOfYear, $studentId, $classId),
            'latestStudentGrades'  => $this->getLatestGrades($latestGradesLimit, $studentId),
            'topClassStudents' => $this->getTopStudents($classId, $topStudentsLimit),
            'classHomeworks' => $this->getClassHomeworks($classId, $classHomeworksLimit),
        ];
    }


    public function getRatings()
    {
        $ratings = Rating::selectRaw('COUNT(*) as count, AVG(' . Rating::RATING_COLUMN . ') as avg')
        ->first();
        
        return (object) [
            'count' => number_format($ratings->count),
            'avg' => $ratings->avg,
        ];
    }

    public function getCharges(Carbon $month, Carbon $monthComparedTo)
    {
        $chargesCollection = $this->collectionOfTwoMonths(Charge::class, $month, $monthComparedTo)
                            ->get([Charge::CREATED_AT, Charge::PRICE_COLUMN, Charge::QUANTITY_COLUMN]);

        $monthCharges = $this->filterByMonth($chargesCollection, $month, Charge::CREATED_AT)
                        ->sum(fn($charge)=>$charge->{Charge::PRICE_COLUMN} * $charge->{Charge::QUANTITY_COLUMN});

        $monthComparedToCharges = $this->filterByMonth($chargesCollection, $monthComparedTo, Charge::CREATED_AT)
                        ->sum(fn($charge)=>$charge->{Charge::PRICE_COLUMN} * $charge->{Charge::QUANTITY_COLUMN});
    
        return $this->formatData($monthCharges, $monthComparedToCharges);
    }

    public function getLatestCharges($limit)
    {
        return Charge::latest()->limit($limit)->get();
    }

    public function getAvgStudentsGrades(Carbon $month, Carbon $monthComparedTo, $teacherId = null)
    {
        $query = $this->collectionOfTwoMonths(Grade::class, $month, $monthComparedTo);
        
        // Grades of a teacher students
        if(isset($teacherId)){
            $query->where(Grade::TEACHER_COLUMN, $teacherId);
        }
        
        $studentGradesCollection = $query->get([Grade::CREATED_AT,Grade::GRADE_COLUMN]);
        
        
        $monthAvgStudentGrade = $this->filterByMonth($studentGradesCollection, $month, Grade::CREATED_AT)
        ->avg(Grade::GRADE_COLUMN);
        
        $monthComparedToAvgStudentGrade = $this->filterByMonth($studentGradesCollection, $monthComparedTo, Grade::CREATED_AT)
        ->avg(Grade::GRADE_COLUMN);
        
        return $this->formatData($monthAvgStudentGrade, $monthComparedToAvgStudentGrade);
    }
    
    public function getTeachers(Carbon $year)
    {
        $teachersCollection = User::teachers()->get([User::CREATED_AT]);

        $currentTeachers = $teachersCollection->count();
        $previousYearTeachers = $teachersCollection->where(User::CREATED_AT, '<', $year->startOfYear())->count();

        return $this->formatData($currentTeachers, $previousYearTeachers);
    }

    public function getStudents(Carbon $year)
    {
        $studentCollection = User::students()->get([User::CREATED_AT, User::GENDER_COLUMN]);

        $currentStudents = $studentCollection->count();
        $previousYearStudents = $studentCollection->where(User::CREATED_AT, '<', $year->startOfYear())->count();
        $maleStudents = $studentCollection->where(User::GENDER_COLUMN, User::GENDER_MALE)->count();
        $femaleStudents = $studentCollection->where(User::GENDER_COLUMN, User::GENDER_FEMALE)->count();
        
        return $this->formatData($currentStudents, $previousYearStudents, [
            'boys' => $maleStudents,
            'girls' => $femaleStudents
        ]);
    }

    public function getAbsences(Carbon $week)
    {
        $lastWeekAbsencesCollection = Absence::whereBetween(Absence::FROM_COLUMN, [
            clone $week,
            clone $week->endOfWeek()->subDay(),
        ])
        ->get([Absence::FROM_COLUMN, Absence::TO_COLUMN]);

        $lastWeekAbsences = [];
        
        // Group the absences by the day of the week
        foreach ($lastWeekAbsencesCollection as $absence) {
            $dayOfWeek = $absence->from->dayOfWeek; // 1 (Monday) to 6 (Saturday)

            if (!isset($lastWeekAbsences[$dayOfWeek])) {
                $lastWeekAbsences[$dayOfWeek] = 0;
            }
            $lastWeekAbsences[$dayOfWeek] += $absence->to->hour - $absence->from->hour;
        }

        return $lastWeekAbsences;
    }

    public function getLatestReports($role, $reportsLimit, $descriptionLimit)
    {
        $relationship = strtolower(UserRole::nameForKey($role)). 'Reports';

        return Report::$relationship()
                ->latest('reports.created_at')
                ->limit($reportsLimit)
                ->selectRaw("LEFT(description, $descriptionLimit) AS shortDescription")
                ->addSelect([
                    'user_name' => User::select(DB::raw("CONCAT(". User::FIRST_NAME_COLUMN . ", ' ', " . User::LAST_NAME_COLUMN . ")"))
                        ->whereColumn('users.id', 'reports.user_id')
                        ->limit(1)
                ])
                ->get();
    }

    public function getTopClasses($limit = 1)
    {
        return Classes::withAvgGrades($this->threeMonthAgo)->orderByDesc(Classes::AVG_GRADES)->limit($limit)->get();
    }

    public function getTeacherClasses(Carbon $year)
    {
        $teacherClassesCollection = auth()->user()->classes;

        $currentTeacherClasses = $teacherClassesCollection->count();
        $previousTeacherClasses = $teacherClassesCollection->where(Classes::CREATED_AT, '<', $year->startOfYear())->count();

        return $this->formatData($currentTeacherClasses, $previousTeacherClasses);
    }

    public function getTeacherStudents(Carbon $year, $classes)
    {
        $teacherStudentsCollection = User::students()->whereIn(User::CLASS_COLUMN, $classes)->get();
        
        $currentTeacherStudents = $teacherStudentsCollection->count();
        $previousTeacherStudents = $teacherStudentsCollection->where(User::CREATED_AT, '<', $year->startOfYear())->count();

        return $this->formatData($currentTeacherStudents, $previousTeacherStudents);
    }

    public function getClassesWithAbsences(Carbon $week, $classes)
    {
       return Classes::select('classes.*')
            ->selectSub(function ($query) use ($week) {
                $query->select(DB::raw(sprintf('SUM(TIMESTAMPDIFF(HOUR, `%s`, `%s`))', Absence::FROM_COLUMN, Absence::TO_COLUMN)))
                    ->from('absences')
                    ->join('users', 'users.id', '=', 'absences.student_id')
                    ->whereColumn('classes.id', 'users.class_id')
                    ->whereBetween(Absence::FROM_COLUMN, [$week, $week->copy()->endOfWeek()->subDay()]);
            }, 'absences_sum')
            ->whereIn('id', $classes)
            ->get();
    }

    public function getTopStudents($classId, $limit)
    {
        $query = User::students();

        // Grades of a teacher students
        if(is_array($classId)){
            $query->whereIn(User::CLASS_COLUMN, $classId);
        }else if(is_int($classId)){
            $query->where(User::CLASS_COLUMN, $classId);
        }

        return  $query
                ->select(['id', User::FIRST_NAME_COLUMN, User::LAST_NAME_COLUMN])
                ->withAvg('grades', Grade::GRADE_COLUMN)
                ->orderByDesc('grades_avg_grade')
                ->limit($limit)
                ->withAggregate('class', Classes::NAME_COLUMN)
                ->get();
    }

    public function getTeacherHomeworks($teacherId, $limit = 1)
    {
        return Homework::withAggregate('class', Classes::NAME_COLUMN)
            ->where(Homework::TEACHER_COLUMN, $teacherId)
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    public function getClassHomeworks($classId, $limit = 1)
    {
        return Homework::with('subject:subjects.name')
            ->where(Homework::CLASS_COLUMN, $classId)
            ->latest()
            ->limit($limit)
            ->get();
    }
    

    public function getLatestGrades($limit = 1, $studentId = null)
    {
        $query = Grade::with('teacher.subject');

        if(isset($studentId)){
            $query->where(Grade::STUDENT_COLUMN, $studentId);
        }
        
        return $query
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getAvgGradesOfEachMonth(Carbon $date, Carbon $toDate, $id, $classId = null)
    {
        $query = Grade::query();

        if(isset($classId)){
            $query->where(Grade::STUDENT_COLUMN, '!=', $id)->whereHas('student', function ($query) use ($classId){
                $query->where(User::CLASS_COLUMN, $classId);
            });
        }else{
            $query->where(Grade::STUDENT_COLUMN, $id);
        }

        return $query
            ->whereBetween(Grade::CREATED_AT, [$date, $toDate])
            ->get([Grade::GRADE_COLUMN, Grade::CREATED_AT])
            ->groupBy(fn($grade)=> Carbon::parse($grade->created_at)->format('M'))
            ->map(fn($monthGrades)=> number_format($monthGrades->avg(Grade::GRADE_COLUMN), 2));
    }

    // -- Auxiliary methods --

    public function collectionOfTwoMonths($model, Carbon $firstMonth, Carbon $secondMonth)
    {
        return $model::where(function($query) use ($model, $firstMonth, $secondMonth) {
            $query->whereMonth($model::CREATED_AT, $firstMonth)
                  ->orWhereMonth($model::CREATED_AT, $secondMonth);
        });
    }

    public function filterByMonth($collection, Carbon $month, string $dateColumn)
    {
        return $collection->whereBetween($dateColumn, 
                [
                    $month->startOfMonth()->toDateTime(),
                    $month->endOfMonth()->toDateTime()
                ]);
    }
    
    public function formatData($currentNumber = 0, $previousNumber = 0, array $extra = []): object
    {
        return (object) [
            'total' => number_format($currentNumber, 2),
            'variation' => $currentNumber - $previousNumber,
            'variation_rate' => $previousNumber !== 0 && isset($previousNumber) ? number_format($currentNumber / $previousNumber * 100 - 100, 2) : null,
            ...$extra
            ];
    }

}
