<?php

namespace App\Http\Controllers;


use App\Models\Subject;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Exports\GradesExport;
use App\Services\GradeService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreGradeRequest;

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
    public function index(Request $request)
    {
        try {
            $subjects = Subject::get([Subject::PRIMARY_KEY_COLUMN_NAME, Subject::NAME_COLUMN]);
            $grades = $this->gradeService->getGrades($request->only(['per-page', 'subject', 'keyword', 'from-date', 'to-date']));

            return view('grades.index', compact('grades', 'subjects'));
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function export(Request $request)
    {
        // Chunk grades and store them temporary until all grades have been get, then delete them after combine them to one file and send it to user
        try {
            $filters = $this->gradeService->relationsBasedonRole($request->only(['subject', 'keyword', 'from-date', 'to-date']));

            $userId = auth()->id();
            $chunkSize = 5000;
            $limitGrades = 50000;

            $gradesCount = $this->gradeService->getGradesQuery($filters)->count();

            $query = $this->gradeService->getGradesQuery($filters)->latest();

            if ($gradesCount > $limitGrades) {
                // Using max id to apply the limit when chunking
                $maxId = $this->gradeService->getGradesQuery($filters)->skip($limitGrades)->take(1)->value('id');
                $query->where('id', '<', $maxId);
            }

            $fileName = 'grades.xlsx';
            $tempFilePattern = '/temp/grades-export-chunk-' . $userId;

            $count = 0;

            // Chunk grades and export them
            $query->chunk($chunkSize, function ($grades) use (&$count, &$tempFilePattern) {
                $count++;
                Excel::store(new GradesExport($grades), $tempFilePattern . "-$count" . '.xlsx', 'public');
            });

            // Merge chunks into a single file
            $combinedFilePath = storage_path('app/public/temp/') . $userId . $fileName;
            GradesExport::mergeExcelFiles($count, $tempFilePattern, $combinedFilePath);

            // Download combined file and delete it after sending
            return response()->download($combinedFilePath, $fileName)->deleteFileAfterSend(true);

        } catch (\Throwable $th) {
            dd($th);
        }
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
