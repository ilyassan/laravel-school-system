<?php

namespace App\Services;

use App\Models\Grade;
use App\Repositories\GradeRepository;

class GradeService
{
    protected $gradeRepository;

    public function __construct(GradeRepository $gradeRepository)
    {
        $this->gradeRepository = $gradeRepository;
    }

    public function getGrades(array $filters)
    {
        $filters = $this->relationsBasedonRole($filters);
        return $this->gradeRepository->getPaginate($filters);
    }

    // To avoid getting data from db that already on the user session
    public function relationsBasedonRole(array $arr)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Relations
        if ($user->isAdmin()) {
            $arr['with'] = [
                'teacher:id,first_name,last_name,subject_id',
                'teacher.subject:id,name',
                'student:id,first_name,last_name,class_id',
                'student.class:id,name',
            ];
        } elseif ($user->isTeacher()) {
            $arr['with'] = [
                'student:id,first_name,last_name,class_id',
                'teacher.subject:id,name',
                'student.class:id,name',
            ];
            $arr['teacher_id'] = $user->id;
        } elseif ($user->isStudent()) {
            $arr['with'] = [
                'teacher:id,first_name,last_name,subject_id',
                'teacher.subject:id,name',
            ];
            $arr['student_id'] = $user->id;
        }

        return $arr;
    }

    public function countGrades(array $filters)
    {
        return $this->gradeRepository->getFilteredQuery($filters)->count();
    }

    public function getGradesQuery(array $filters)
    {
        return $this->gradeRepository->getFilteredQuery($filters);
    }

    public function createGrade(array $data)
    {
        return Grade::create($data);
    }
}
