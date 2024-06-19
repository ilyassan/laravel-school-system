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
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Relations
        if ($user->isAdmin()) {
            $filters['with'] = [
                'teacher:id,first_name,last_name,subject_id',
                'student:id,first_name,last_name,class_id',
                'student.class:id,name',
            ];
        } elseif ($user->isTeacher()) {
            $filters['with'] = [
                'student:id,first_name,last_name,class_id',
                'student.class:id,name',
            ];
            $filters['teacher_id'] = $user->id;
        } elseif ($user->isStudent()) {
            $filters['with'] = [
                'teacher:id,first_name,last_name,subject_id',
            ];
            $filters['student_id'] = $user->id;
        }

        return $this->gradeRepository->getPaginate($filters);
    }

    public function createGrade(array $data)
    {
        return Grade::create($data);
    }
}
