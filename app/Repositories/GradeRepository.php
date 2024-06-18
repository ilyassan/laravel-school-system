<?php

namespace App\Repositories;

use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GradeRepository extends AbstractRepository
{
    public function model()
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

    protected function applyFilters($query, array $filters)
    {
        $this->applySubjectFilter($query, Arr::get($filters, 'subject'));
        $this->applyKeywordSearch($query, Arr::get($filters, 'keyword'));
        $this->applyDateFilters($query, Arr::get($filters, 'from-date'), Arr::get($filters, 'to-date'));
    }


    // Subject Filter
    protected function applySubjectFilter($query, $subjectId)
    {
        if ($subjectId) {
            $query->whereHas('teacher.subject', function ($q) use ($subjectId) {
                $q->where(Subject::PRIMARY_KEY_COLUMN_NAME, $subjectId);
            });
        }
    }


    // Keyword search
    protected function applyKeywordSearch($query, $keyword)
    {
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                // Filter by fullname
                $q->whereHas('student', function ($q) use ($keyword) {
                    $q->whereFullNameLike($keyword)

                        // Filter by class
                        ->orWhereHas('class', function ($q) use ($keyword) {
                            $q->where('name', $keyword);
                        });
                })
                    // Filter by fullname
                    ->orWhereHas('teacher', function ($q) use ($keyword) {
                        $q->whereFullNameLike($keyword);
                    });
            });
        }
    }


    // Date filters
    protected function applyDateFilters($query, $fromDate, $toDate)
    {
        if ($fromDate) {
            $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($fromDate)));
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($toDate)));
        }
    }
}
