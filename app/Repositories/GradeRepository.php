<?php

namespace App\Repositories;

use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class GradeRepository extends AbstractRepository
{
    public function model(): string
    {
        return Grade::class;
    }

    public function getPaginate(array $filters): LengthAwarePaginator
    {
        $query = $this->model::query();

        $this->applyFilters($query, $filters);

        $perPage = (int) Arr::get($filters, 'per-page', 10);
        $with = Arr::get($filters, 'with', []);

        return $query->with($with)->latest()->paginate($perPage);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if ($teacherId = Arr::get($filters, 'teacher_id')) {
            $query->where('teacher_id', $teacherId);
        } elseif ($studentId = Arr::get($filters, 'student_id')) {
            $query->where('student_id', $studentId);
        }

        if ($subjectId = Arr::get($filters, 'subject')) {
            $query->whereHas('teacher.subject', function (Builder $q) use ($subjectId) {
                $q->where(Subject::PRIMARY_KEY_COLUMN_NAME, $subjectId);
            });
        }
        if ($keyword = Arr::get($filters, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                // Filter by fullname
                $q->whereHas('student', function (Builder $q) use ($keyword) {
                    $q->whereFullNameLike($keyword)
                        // Filter by class
                        ->orWhereHas('class', function (Builder $q) use ($keyword) {
                            $q->where('name', $keyword);
                        });
                })
                    // Filter by fullname
                    ->orWhereHas('teacher', function (Builder $q) use ($keyword) {
                        $q->whereFullNameLike($keyword);
                    });
            });
        }
        if ($fromDate = Arr::get($filters, 'from-date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d'));
        }

        if ($toDate = Arr::get($filters, 'to-date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d'));
        }
    }
}
