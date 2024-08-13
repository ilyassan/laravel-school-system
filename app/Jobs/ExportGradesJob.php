<?php

namespace App\Jobs;

use App\Models\Grade;
use App\Enums\ExportStatus;
use Illuminate\Support\Arr;
use App\Exports\GradesExport;
use Illuminate\Bus\Queueable;
use App\Services\GradeService;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\GradeRepository;
use Illuminate\Support\Facades\Cache;
use App\Events\ExportProcessCompleted;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportGradesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $sorting;
    protected $exportId;
    protected $user;
    protected $exportStatus;
    protected $gradesLimit;
    protected $chunkSize;

    public function __construct($filters, $sorting, $exportStatus, $exportId, $user)
    {
        $this->filters = $filters;
        $this->sorting = $sorting;
        $this->exportStatus = $exportStatus;
        $this->exportId = $exportId;
        $this->user = $user;
        $this->gradesLimit = 100000;
        $this->chunkSize = 5000;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        GradesExport::ensureTempsFolderExists();
        GradesExport::ensureDownloadsFolderExists();

        $tempFilePattern = 'grades-export-chunk-' . $this->exportId;

        $gradeService = app(GradeService::class);
        $gradeRepo = app(GradeRepository::class);

        $gradesQuery = $gradeService->getGradesQuery($this->filters);
        $gradesCount = $gradeService->countGrades($this->filters);

        $query = clone $gradesQuery;

        // Sorting Grades
        list($sortingColumnQuery, $sortDirection) = $gradeRepo->sortGradesQuery($this->sorting);

        $query->orderBy($sortingColumnQuery, $sortDirection);

        if ($gradesCount > $this->gradesLimit) {
            // To implement limit for the query chunking
            $lastTimestamp = $gradesQuery->skip($this->gradesLimit)->take(1)->value(Grade::CREATED_AT);
            $query->where(Grade::CREATED_AT, '>', $lastTimestamp);
        }

        // Chunking
        $count = 0;
        $query->chunk($this->chunkSize, function ($grades) use (&$count, &$tempFilePattern) {
            if (Cache::get($this->exportStatus . $this->exportId)['status'] === ExportStatus::CANCELLED) {
                return false;
            }
            $count++;
            Excel::store(new GradesExport($grades, $this->user), GradesExport::$tempFolder . '/' . $tempFilePattern . "-$count" . '.xlsx', 'public');
        });

        $fileName = GradesExport::getUniqueDownloadFileName($this->exportId);
        $combinedFilePath = GradesExport::getDownloadFolderPath() . $fileName;

        GradesExport::mergeExcelFiles($count, $tempFilePattern, $combinedFilePath, $this->exportStatus . $this->exportId);

        $cacheStatus = Cache::get($this->exportStatus . $this->exportId)['status'];
        if ($cacheStatus === ExportStatus::CANCELLED) {
            Cache::forget($this->exportStatus . $this->exportId);
        } else if ($cacheStatus === ExportStatus::IN_PROGRESS) {
            Cache::put($this->exportStatus . $this->exportId, ['status' => ExportStatus::COMPLETED], now()->addMinutes(10));
            broadcast(new ExportProcessCompleted($this->exportId, $fileName));
        }
    }

}
