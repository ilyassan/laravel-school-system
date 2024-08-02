<?php

namespace App\Console\Commands;

use App\Exports\BaseExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupUndownloadedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup-undownloaded-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove files from the downloads folder that are older than 30 minutes and have not been downloaded by users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $folderPath = BaseExport::getDownloadFolderPath(); // Path to the downloads folder

        $maxAge = now()->subMinutes(10)->timestamp * 1000; // milleseconds

        $files = glob($folderPath . '*');

        foreach ($files as $file) {
            // Extract the filename from the file path
            $fileName = basename($file);

            // Extract the timestamp from the filename
            $fileTimestamp = BaseExport::getIdFromFileName($fileName);

            if ($fileTimestamp && $fileTimestamp < $maxAge) {
                // File is older than 30 minutes, so delete it
                if (unlink($file)) {
                    Log::info("Deleted file: $file");
                } else {
                    Log::error("Failed to delete file: $file");
                }
            }
        }
    }
}