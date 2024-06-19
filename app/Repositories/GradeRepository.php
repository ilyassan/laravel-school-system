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

        $this->checkRole($query);
        $this->applyFilters($query, $filters);

        $perPage = (int) Arr::get($filters, 'per-page', 10);
        $with = Arr::get($filters, 'with', []);

        return $query->with($with)->latest()->paginate($perPage);
    }

    protected function checkRole(Builder $query): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->isTeacher()) {
            $query->where('teacher_id', $user->id);
        } elseif ($user->isStudent()) {
            $query->where('student_id', $user->id);
        }
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySubjectFilter($query, Arr::get($filters, 'subject'));
        $this->applyKeywordSearch($query, Arr::get($filters, 'keyword'));
        $this->applyDateFilters($query, Arr::get($filters, 'from-date'), Arr::get($filters, 'to-date'));
    }

    protected function applySubjectFilter(Builder $query, ?int $subjectId): void
    {
        if ($subjectId) {
            $query->whereHas('teacher.subject', function (Builder $q) use ($subjectId) {
                $q->where(Subject::PRIMARY_KEY_COLUMN_NAME, $subjectId);
            });
        }
    }

    protected function applyKeywordSearch(Builder $query, ?string $keyword): void
    {
        if ($keyword) {
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
    }

    protected function applyDateFilters(Builder $query, ?string $fromDate, ?string $toDate): void
    {
        if ($fromDate) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d'));
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('m/d/Y', $toDate)->format('Y-m-d'));
        }
    }
}
