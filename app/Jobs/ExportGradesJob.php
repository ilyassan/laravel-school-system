<?php

namespace App\Jobs;

use App\Enums\ExportStatus;
use App\Exports\GradesExport;
use Illuminate\Bus\Queueable;
use App\Services\GradeService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportGradesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $exportId;
    protected $user;
    protected $exportStatus;

    public function __construct($filters, $exportStatus, $exportId, $user)
    {
        $this->filters = $filters;
        $this->exportStatus = $exportStatus;
        $this->exportId = $exportId;
        $this->user = $user;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tempFilePattern = '/temp/grades-export-chunk-' . $this->exportId;
        $chunkSize = 6000;
        $limitGrades = 40000;

        $gradesCount = app(GradeService::class)->getGradesQuery($this->filters)->count();
        $query = app(GradeService::class)->getGradesQuery($this->filters)->latest();

        if ($gradesCount > $limitGrades) {
            $maxId = app(GradeService::class)->getGradesQuery($this->filters)->skip($limitGrades)->take(1)->value('id');
            $query->where('id', '<', $maxId);
        }

        $count = 0;
        $query->chunk($chunkSize, function ($grades) use (&$count, &$tempFilePattern, &$exportStatus) {
            if (Cache::get($this->exportStatus . $this->exportId)['status'] === ExportStatus::CANCELLED) {
                return false;
            }
            $count++;
            Excel::store(new GradesExport($grades, $this->user), $tempFilePattern . "-$count" . '.xlsx', 'public');
        });

        $fileName = $this->exportId . '-' . 'grades.xlsx';
        $combinedFilePath = storage_path('app/public/temp/') . $fileName;
        GradesExport::mergeExcelFiles($count, $tempFilePattern, $combinedFilePath, $this->exportStatus . $this->exportId);

        // Clear cache entry after completion
        Cache::forget($this->exportStatus . $this->exportId);

        //broadcasting filename when it finish // nothing if it cancelled
        //delete files automaticly after 10 minutes
        // return response()->json(['file_name' => $fileName]);
    }
}
