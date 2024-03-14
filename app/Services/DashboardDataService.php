<?php

namespace App\Services;

use Carbon\Carbon;
use App\Enums\UserRole;
use App\Models\{User, Grade, Charge, Report, Absence, Classes, Rating};

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

    public function adminDashboardData(){
        
        $latestChargesLimit = 5;
        $latestReportsLimit= 3;
        $topClassesLimit= 5;
        $teacherReportsDescriptionChars= 80;
        $studentReportsDescriptionChars= 140;

        return [
            'ratings' => $this->getRatings(),
            'charges' => $this->getCharges($this->currentMonth, $this->previousMonth),
            'latestCharges' => $this->getLatestCharges($latestChargesLimit),
            'avgStudentGrade' => $this->getAvgStudentGrades($this->currentMonth, $this->previousMonth),
            'teachers' => $this->getTeachers($this->currentYear),
            'students' => $this->getStudents($this->currentYear),
            'lastWeekAbsences' => $this->getAbsences($this->lastWeek),
            'latestTeacherReports' => $this->getLatestReports(UserRole::TEACHER, $latestReportsLimit, $teacherReportsDescriptionChars),
            'latestStudentsReports' => $this->getLatestReports(UserRole::STUDENT, $latestReportsLimit, $studentReportsDescriptionChars),
            'topClasses' => $this->getTopClasses($topClassesLimit)
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
        $chargesCollection = $this->getCollectionOfTwoMonths(Charge::class, $month, $monthComparedTo, [Charge::CREATED_AT, Charge::PRICE_COLUMN, Charge::QUANTITY_COLUMN]);

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

    public function getAvgStudentGrades(Carbon $month, Carbon $monthComparedTo)
    {
        $studentGradesCollection = $this->getCollectionOfTwoMonths(Grade::class, $month, $monthComparedTo, [Grade::CREATED_AT, Grade::GRADE_COLUMN]);
        
        $monthAvgStudentGrade = $this->filterByMonth($studentGradesCollection, $month, Grade::CREATED_AT)->avg(Grade::GRADE_COLUMN);
        $monthComparedToAvgStudentGrade = $this->filterByMonth($studentGradesCollection, $monthComparedTo, Grade::CREATED_AT)->avg(Grade::GRADE_COLUMN);

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
        ])->get([Absence::FROM_COLUMN, Absence::TO_COLUMN]);

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

        return Report::$relationship()->latest('reports.created_at')->limit($reportsLimit)
                    ->selectRaw("LEFT(description, $descriptionLimit) AS shortDescription")
                    ->withAggregate('user', User::NAME_COLUMN)
                    ->get();
    }

    public function getTopClasses($limit)
    {
        return Classes::withAvgGrades($this->threeMonthAgo)->orderByDesc(Classes::AVG_GRADES)->limit($limit)->get();
    }


    // -- Auxiliary methods --

    public function getCollectionOfTwoMonths($model, Carbon $firstMonth, Carbon $secondMonth , array $columnsToGet = ['*'])
    {
        return $model::whereMonth($model::CREATED_AT, $firstMonth)
                    ->orWhereMonth($model::CREATED_AT, $secondMonth)
                    ->get($columnsToGet);
    }

    public function filterByMonth($collection, Carbon $month, string $dateColumn)
    {
        return $collection->whereBetween($dateColumn, [$month->startOfMonth()->toDateTime(), $month->endOfMonth()->toDateTime()]);
    }
    
    public function formatData($currentNumber, $previousNumber, array $extra = []): object
    {
        return (object) [
            'total' => number_format($currentNumber, 2),
            'variation' => $currentNumber - $previousNumber,
            'variation_rate' => $previousNumber !== 0 ? number_format($currentNumber / $previousNumber * 100 - 100, 2) : null,
            ...$extra
            ];
    }

}
