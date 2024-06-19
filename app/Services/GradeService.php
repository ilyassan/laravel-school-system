<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Subject;
use App\Repositories\GradeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeService
{
    protected $gradeRepository;

    public function __construct(GradeRepository $gradeRepository)
    {
        $this->gradeRepository = $gradeRepository;
    }

    public function validateFilters(Request $request)
    {
        // Validate filter inputs

        $subjects = Subject::get([Subject::PRIMARY_KEY_COLUMN_NAME, Subject::NAME_COLUMN]);

        $validator = Validator::make(
            $request->all(),
            [
                'per-page' => ['nullable', 'integer', 'max:100', 'min:10'],
                'subject' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($subjects) {
                        if (!in_array($value, $subjects->pluck('id')->toArray())) {
                            return $fail('Selected Subject does not exist');
                        }
                    }
                ],
                'keyword',
                'from-date' => ['nullable', 'date_format:m/d/Y'],
                'to-date' => ['nullable', 'date_format:m/d/Y'],
            ]
        );

        return [$validator, $subjects];
    }

    public function getGrades(Request $request, array $filters)
    {
        // Relations
        $filters['with'] = [
            'teacher:id,first_name,last_name,subject_id',
            'teacher.subject:id,name',
            'student:id,first_name,last_name,class_id',
            'student.class:id,name',
        ];

        return $this->gradeRepository->getPaginate($filters);
    }

    public function createGrade(array $data)
    {
        return Grade::create($data);
    }
}
