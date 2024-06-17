<?php

namespace App\Repositories;

use DateTime;
use App\Models\User;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        $perPage = (int) Arr::get($filters, 'per-page', 10);
        $with = Arr::get($filters,'with',[]);

        // Filter by subject
        if($subject = Arr::get($filters, 'subject')){
            $query->whereHas('teacher.subject', function ($q) use($subject){
                $q->where(sprintf('%s.%s', Subject::TABLE, Subject::PRIMARY_KEY_COLUMN_NAME), $subject);
            });
        }

        if($keyword = Arr::get($filters, 'keyword')){
            $query->where(function($q) use($keyword){
                // Filter by fullname
                $q->whereHas('student', function ($q) use($keyword){
                    $q->where(DB::raw(sprintf("CONCAT(%s.%s, ' ', %s.%s)", User::TABLE, User::FIRST_NAME_COLUMN, User::TABLE, User::LAST_NAME_COLUMN)), 'LIKE', '%'.$keyword.'%')

                // Filter by class
                    ->orWhereHas('class', function ($q) use ($keyword){
                        $q->where('name', $keyword);
                    });
                })
                // Filter by fullname
                ->orWhereHas('teacher', function ($q) use($keyword){
                    $q->where(DB::raw(sprintf("CONCAT(%s.%s, ' ', %s.%s)", User::TABLE, User::FIRST_NAME_COLUMN, User::TABLE, User::LAST_NAME_COLUMN)), 'LIKE', '%'.$keyword.'%');
                });
            });
        }

        // Filter by dates
        if( Arr::get($filters, 'from-date')) {

            $fromDate = Arr::get($filters, 'from-date', '01/01/1970');
            $fromDate = date('Y-m-d', strtotime($fromDate));
            
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if(Arr::get($filters, 'to-date')){
            $toDate = Arr::get($filters, 'to-date', '12/02/2025');
            $toDate = date('Y-m-d', strtotime($toDate));

            $query->whereDate('created_at', '<=', $toDate);
        }

        return $query->with($with)->latest()->paginate($perPage);
    }

}
