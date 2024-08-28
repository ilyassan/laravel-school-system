<?php

namespace App\Services;

use App\Enums\UserGender;
use Carbon\Carbon;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;

use App\Models\{User, Grade, Invoice, Report, Absence, Classes, Homework, Rating};

class DashboardService
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
        $latestInvoicesLimit = 5;
        $latestReportsLimit = 3;
        $topClassesLimit = 5;
        $teacherReportsDescriptionChars = 80;
        $studentReportsDescriptionChars = 140;

        return [
            'ratings' => $this->getRatings(),
            'charges' => $this->getCharges($this->currentMonth, $this->previousMonth),
            'latestInvoices' => $this->getLatestInvoices($latestInvoicesLimit),
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

        /** @var \App\Models\User */
        $teacher = auth()->user();
        $teacherId = $teacher->getKey();
        $teacherClasses = $teacher->classes->pluck(User::PRIMARY_KEY_COLUMN_NAME);

        return [
            'ratings' => $this->getRatings(),
            'teacherClasses' => $this->getTeacherClasses($this->currentYear),
            'teacherStudentsAvgGrades' => $this->getAvgStudentsGrades($this->currentMonth, $this->previousMonth, $teacherId),
            'teacherStudents' => $this->getTeacherStudents($this->currentYear, $teacherClasses),
            'salary' => $teacher->getSalary(),
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

        /** @var \App\Models\User */
        $student = auth()->user();

        $studentId = $student->getKey();
        $classId = $student->getClassId();

        $startOfYear = $this->currentYear;
        $endOfYear = $this->currentYear->clone()->endOfYear();

        return [
            'ratings' => $this->getRatings(),
            'avgStudentGradesEachMonth' => $this->getAvgGradesOfEachMonth($startOfYear, $endOfYear, $studentId),
            'avgClassGradesEachMonth' => $this->getAvgGradesOfEachMonth($startOfYear, $endOfYear, $studentId, $classId),
            'latestStudentGrades' => $this->getLatestGrades($latestGradesLimit, $studentId),
            'topClassStudents' => $this->getTopStudents($classId, $topStudentsLimit),
            'classHomeworks' => $this->getClassHomeworks($classId, $classHomeworksLimit),
        ];
    }


    public function getRatings()
    {
        $ratings = Rating::selectRaw('COUNT(*) as count, AVG(' . Rating::RATING_COLUMN . ') as avg')->first();

        return (object) [
            'count' => number_format($ratings->count),
            'avg' => $ratings->avg,
        ];
    }

    public function getCharges(Carbon $month, Carbon $monthComparedTo)
    {
        $chargesCollection = $this->collectionOfTwoMonths(Invoice::class, $month, $monthComparedTo);

        $monthCharges = $this->filterByMonth(clone $chargesCollection, $month, Invoice::CREATED_AT)
            ->sum(DB::raw(Invoice::PRICE_COLUMN . ' * ' . Invoice::QUANTITY_COLUMN));

        $monthComparedToCharges = $this->filterByMonth($chargesCollection, $monthComparedTo, Invoice::CREATED_AT)
            ->sum(DB::raw(Invoice::PRICE_COLUMN . ' * ' . Invoice::QUANTITY_COLUMN));

        return $this->formatData($monthCharges, $monthComparedToCharges);
    }

    public function getLatestInvoices($limit)
    {
        return Invoice::latest()->limit($limit)->get();
    }

    public function getAvgStudentsGrades(Carbon $month, Carbon $monthComparedTo, $teacherId = null)
    {
        $query = $this->collectionOfTwoMonths(Grade::class, $month, $monthComparedTo);

        // Grades of a teacher students
        if (isset($teacherId)) {
            $query->where(Grade::TEACHER_COLUMN, $teacherId);
        }

        $monthAvgStudentGrade = $this->filterByMonth(clone $query, $month, Grade::CREATED_AT)
            ->avg(Grade::GRADE_COLUMN);

        $monthComparedToAvgStudentGrade = $this->filterByMonth(clone $query, $monthComparedTo, Grade::CREATED_AT)
            ->avg(Grade::GRADE_COLUMN);

        return $this->formatData($monthAvgStudentGrade, $monthComparedToAvgStudentGrade);
    }

    public function getTeachers(Carbon $year)
    {
        $teachersQuery = User::teachers();

        $currentTeachers = (clone $teachersQuery)->count();
        $previousYearTeachers = $teachersQuery->where(User::CREATED_AT, '<', $year->startOfYear())->count();

        return $this->formatData($currentTeachers, $previousYearTeachers);
    }

    public function getStudents(Carbon $year)
    {
        $studentsQuery = User::students();

        $currentStudents = (clone $studentsQuery)->count();
        $previousYearStudents = (clone $studentsQuery)->where(User::CREATED_AT, '<', $year->startOfYear())->count();
        $maleStudents = (clone $studentsQuery)->where(User::GENDER_COLUMN, UserGender::GENDER_MALE)->count();
        $femaleStudents = $studentsQuery->where(User::GENDER_COLUMN, UserGender::GENDER_FEMALE)->count();

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
        $relationship = strtolower(UserRole::nameForKey($role)) . 'Reports';

        return Report::$relationship()
            ->latest(Report::CREATED_AT)
            ->with('user')
            ->selectRaw("LEFT(" . Report::DESCRIPTION_COLUMN . ", $descriptionLimit) AS shortDescription, user_id")
            ->limit($reportsLimit)
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
        if (!is_array($classId)) {
            $classId = (array) $classId;
        }
        $query = User::students();

        // Grades of a teacher students
        $query->whereIn(User::CLASS_COLUMN, $classId);

        return $query
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

        if (isset($studentId)) {
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

        if (isset($classId)) {
            $query->where(Grade::STUDENT_COLUMN, '!=', $id)->whereHas('student', function ($query) use ($classId) {
                $query->where(User::CLASS_COLUMN, $classId);
            });
        } else {
            $query->where(Grade::STUDENT_COLUMN, $id);
        }

        return $query
            ->whereBetween(Grade::CREATED_AT, [$date, $toDate])
            ->select(DB::raw("MONTH(" . Grade::CREATED_AT . ") as month"), DB::raw("AVG(" . Grade::GRADE_COLUMN . ") as avg_grade"))
            ->groupBy(DB::raw("MONTH(" . Grade::CREATED_AT . ")"))
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::createFromFormat('m', $item->month)->format('M') => number_format($item->avg_grade, 2)];
            });
    }

    // -- Auxiliary methods --

    public function collectionOfTwoMonths($model, Carbon $firstMonth, Carbon $secondMonth)
    {
        return $model::where(function ($query) use ($model, $firstMonth, $secondMonth) {
            $query->whereMonth($model::CREATED_AT, $firstMonth)
                ->orWhereMonth($model::CREATED_AT, $secondMonth);
        });
    }

    public function filterByMonth($query, Carbon $month, string $dateColumn)
    {
        return $query->whereBetween(
            $dateColumn,
            [
                $month->startOfMonth()->toDateTime(),
                $month->endOfMonth()->toDateTime()
            ]
        );
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
