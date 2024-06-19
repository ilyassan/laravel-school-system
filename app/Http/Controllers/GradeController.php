<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGradeRequest;
use App\Services\GradeService;

class GradeController extends BaseController
{
    private $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->middleware("teacher")->only(["create", "store", "edit", "update", "destroy"]);
        $this->gradeService = $gradeService;
    }

    /**
     * Display a listing of the grade.
     */
    public function index(Request $request): View
    {
        list($validator, $subjects) = $this->gradeService->validateFilters($request);

        if ($validator->fails()) {
            $invalidFilters = array_keys($validator->errors()->messages());
            $filters = $request->except($invalidFilters);
            foreach ($invalidFilters as $invalid) {
                $request[$invalid] = null;
            }
        } else {
            $filters = $request->only(['per-page', 'subject', 'keyword', 'from-date', 'to-date']);
        }

        $grades = $this->gradeService->getGrades($request, $filters);

        return view('grades.index', compact('grades', 'subjects'))->with('invalidFilter', $validator->errors()->all());
    }


    /**
     * Show the form for creating a new grade.
     */
    public function create(): View
    {
        $classes = $this->getAuthUser()->classes;

        return view('grades.create', compact('classes'));
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(StoreGradeRequest $request): RedirectResponse
    {
        $this->gradeService->createGrade(
            [
                'teacher_id' => $this->getAuthUser()->id,
                ...$request->validated()
            ]
        );

        return redirect()->back()->with('success', 'The grade created successfully.')->withInput(['class_id' => $request->get('class_id')]);
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
