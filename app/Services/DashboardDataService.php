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
        
        $teacherClasses = auth()->user()->classes->pluck('id');
        return [
            'teacherClasses' => $this->getTeacherClasses($this->currentYear),
            'teacherStudentsAvgGrades' => $this->getAvgStudentsGrades($this->currentMonth, $this->previousMonth, auth()->id()),
            'teacherStudents' => $this->getTeacherStudents($this->currentYear, $teacherClasses),
            'salary' => auth()->user()->salary,
            'teacherClassesWithAbsences' => $this->getClassesWithAbsences($this->lastWeek, $teacherClasses),
            'topTeacherStudents' => $this->getTopStudents($teacherClasses, $topStudentsLimit),
            'latestHomeworks' => $this->getHomeworks(auth()->id(), $latestHomeworksLimit),
        ];
    }

    public function studentDashboardData()
    {
        $latestGradesLimit = 5;

        $otherClassStudentIds = auth()->user()->class->students->pluck('id')->except(auth()->id());
        return [
           'latestStudentGrades'  => $this->getLatestGrades(auth()->id(), $latestGradesLimit),
           'avgStudentGradesEachMonth'  => $this->getAvgGradesEachMonth(auth()->id(), $this->currentYear, $this->currentYear->clone()->endOfYear()),
           'avgClassGradesEachMonth'  => $this->getAvgGradesEachMonth($otherClassStudentIds, $this->currentYear, $this->currentYear->clone()->endOfYear()),
        ];
    }


    public function getRatings()
    {
        $ratingsCollection = Rating::all(Rating::RATING_COLUMN);

        return  (object) [
            'count' => number_format($ratingsCollection->count()),
            'avg' => $ratingsCollection->avg(Rating::RATING_COLUMN)
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
                ->withAggregate('user', User::NAME_COLUMN)
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

    public function getTopStudents($classes = null, $limit = 1)
    {
        $query = User::students();

        // Grades of a teacher students
        if(isset($classes)){
            $query->whereIn(User::CLASS_COLUMN, $classes);
        }

        return  $query
                ->withAvg('grades', Grade::GRADE_COLUMN)
                ->orderByDesc('grades_avg_grade')
                ->limit($limit)
                ->withAggregate('class', Classes::NAME_COLUMN)
                ->get();
    }

    public function getHomeworks($teacherId = null, $limit = 1)
    {
        $query = Homework::
                withAggregate('class', Classes::NAME_COLUMN)
                ->latest();
        if(isset($teacherId)){
            $query->where(Homework::TEACHER_COLUMN, $teacherId);
        }

        return $query
                ->limit($limit)
                ->get();
    }

    public function getLatestGrades($studentId = null, $limit = 1)
    {
        $query = Grade::with('teacher.subject');

        if(isset($studentId)){
            $query->where('student_id', $studentId);
        }
        
        return $query
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getAvgGradesEachMonth($id = null, Carbon $date, Carbon $toDate)
    {
        $query = Grade::query();

        if(is_array($id)){
            $query->whereIn(Grade::STUDENT_COLUMN, $id);
        }else if(is_int($id)){
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
