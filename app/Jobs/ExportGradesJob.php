<?php

namespace App\Jobs;

use App\Models\Grade;
use App\Enums\ExportStatus;
use App\Exports\GradesExport;
use Illuminate\Bus\Queueable;
use App\Services\GradeService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Events\ExportProcessCompleted;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use function Symfony\Component\String\b;

class ExportGradesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $exportId;
    protected $user;
    protected $exportStatus;
    protected $gradesLimit;
    protected $chunkSize;

    public function __construct($filters, $exportStatus, $exportId, $user)
    {
        $this->filters = $filters;
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
        $gradesQuery = $gradeService->getGradesQuery($this->filters)->latest();
        $gradesCount = $gradeService->countGrades($this->filters);

        $query = clone $gradesQuery;

        if ($gradesCount > $this->gradesLimit) {
            // To implement limit for the query chunking
            $lastTimestamp = $gradesQuery->skip($this->gradesLimit)->take(1)->value('created_at');
            $query->where(Grade::CREATED_AT, '>', $lastTimestamp);
        }

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
