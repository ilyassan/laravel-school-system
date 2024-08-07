<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\View\View;
use App\Enums\ExportStatus;
use Illuminate\Http\Request;
use App\Exports\GradesExport;
use App\Jobs\ExportGradesJob;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreGradeRequest;

class GradeController extends BaseController
{
    private $gradeService;
    private $filterInputs;
    private $exportStatus;

    public function __construct(GradeService $gradeService)
    {
        $this->middleware("teacher")->only(["create", "store", "edit", "update", "destroy"]);
        $this->gradeService = $gradeService;
        $this->filterInputs = ['per-page', 'subject', 'keyword', 'from-date', 'to-date'];
        $this->exportStatus = 'export-status-';
    }

    /**
     * Display a listing of the grade.
     */
    public function index(Request $request)
    {
        try {
            $subjects = Subject::get([Subject::PRIMARY_KEY_COLUMN_NAME, Subject::NAME_COLUMN]);
            $grades = $this->gradeService->getGrades($request->only($this->filterInputs));

            return view('grades.index', compact('grades', 'subjects'));
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

        return redirect()->back()->with('message', 'The grade created successfully.')->withInput(['class_id' => $request->get('class_id')]);
    }

    /**
     * Display the specified grade.
     */
    public function show(string $id)
    {
        $grade = $this->gradeService->getGrade($id);

        return view('grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(string $id)
    {
        $grade = $this->gradeService->getGrade($id);

        return view('grades.edit', compact('grade'));
    }

    /**
     * Update the specified grade in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'grade' => ['required', 'numeric', 'min:0', 'max:20'],
        ]);

        $this->gradeService->updateGrade($validatedData, $id);

        return redirect()->route('grades.show', $id)->with('message', 'The grade has been updated successfully.');
    }

    /**
     * Remove the specified grade from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Export process methods.
     */

    public function export(Request $request)
    {
        $exportId = $request->export_id;

        // Store export ID in cache with "in-progress" status
        Cache::put($this->exportStatus . $exportId, ['status' => ExportStatus::IN_PROGRESS], now()->addMinutes(3));

        $filters = $this->gradeService->relationsBasedonRole($request->only($this->filterInputs));

        // Dispatch the export job
        ExportGradesJob::dispatch($filters, $this->exportStatus, $exportId, $this->getAuthUser());

        return response()->json(['message' => 'Export has been queued.']);
    }

    public function cancelExport(Request $request)
    {
        $request->validate([
            'export_id' => 'required|string',
        ]);

        $exportId = $request->input('export_id');

        // Set the export status to canceled in cache
        Cache::put($this->exportStatus . $exportId, ['status' => ExportStatus::CANCELLED], now()->addMinutes(3));

        return response()->json(['message' => 'Export canceled']);
    }

    public function download(Request $request)
    {
        $request->validate([
            'export_id' => 'required|string',
            'file_name' => 'required|string',
        ]);

        $exportId = $request->input('export_id');
        $fileName = $request->input('file_name');

        $exportStatusId = $this->exportStatus . $exportId;
        $status = Cache::get($exportStatusId);

        if ($status && $status['status'] !== ExportStatus::COMPLETED) {
            Cache::forget($exportStatusId);
            return redirect()->route('grades.index');
        }
        // Export status is completed

        Cache::forget($exportStatusId);

        $filePath = GradesExport::getDownloadFolderPath() . $fileName;

        if (!file_exists($filePath)) {
            return abort(404);
        }

        try {
            return response()->download($filePath, 'grades.xlsx');
        } catch (\Throwable $th) {
            return abort(500);
        }
    }
}
