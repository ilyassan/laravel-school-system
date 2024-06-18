<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Subject;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Repositories\GradeRepository;
use App\Http\Requests\StoreGradeRequest;
use Illuminate\Support\Facades\Validator;

class GradesController extends BaseController
{
    private $gradeRepository;

    public function __construct(GradeRepository $gradeRepository)
    {
        $this->gradeRepository = $gradeRepository;
    }

    /**
     * Display a listing of the grade.
     */
    public function index(Request $request): View
    {
        $subjects = Subject::get([Subject::PRIMARY_KEY_COLUMN_NAME, Subject::NAME_COLUMN]);

        // Check Valid Filters Inputs
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

        if ($validator->fails()) {
            $invalidFilters = array_keys($validator->errors()->messages());
            $filters = $request->except($invalidFilters);
            foreach ($invalidFilters as $invalid) {
                $request[$invalid] = null;
            }

        } else {
            $filters = $request->only(['per-page', 'subject', 'keyword', 'from-date', 'to-date']);
        }

        // Relations
        $filters['with'] = [
            'teacher:id,first_name,last_name,subject_id',
            'teacher.subject:id,name',
            'student:id,first_name,last_name,class_id',
            'student.class:id,name',
        ];

        $grades = $this->gradeRepository->getPaginate($filters);

        return view('grades.index', compact('grades', 'subjects'))->with('invalidFilter', $validator->errors()->all());
    }


    /**
     * Show the form for creating a new grade.
     */
    public function create()
    {
        $classes = $this->getAuthUser()->classes;

        return view('grades.create', compact('classes'));
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(StoreGradeRequest $request)
    {
        Grade::create([
            'teacher_id' => $this->getAuthUser()->id,
            'student_id' => $request->get('student-id'),
            'grade' => $request->get('grade'),
        ]);

        return redirect()->route('grades.create')->with('success', 'The grade created successfully.')->withInput(['class-id' => $request->get('class-id')]);
    }

    /**
     * Display the specified grade.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified grade in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified grade from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
