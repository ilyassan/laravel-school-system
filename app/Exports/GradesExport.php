<?php

namespace App\Exports;

use App\Enums\UserRole;
use App\Enums\ExportStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Support\Facades\Log;

class GradesExport extends BaseExport implements FromCollection, WithHeadings
{
    protected $coll;
    protected $user;
    public static $downloadFileName = 'grades.xlsx';
    public static $chunkSize = 10000;

    public function __construct($coll, $user)
    {
        $this->coll = $coll;
        $this->user = $user;
    }

    public static function getUniqueDownloadFileName($uniqueId)
    {
        return $uniqueId . '-' . self::$downloadFileName;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $collection = $this->coll;

        switch ($this->user->getRoleId()) {
            case UserRole::ADMIN:
                return $collection->map(
                    fn($grade) => $this->adminRowData($grade)
                );
            case UserRole::TEACHER:
                return $collection->map(
                    fn($grade) => $this->teacherRowData($grade, $this->user)
                );
            case UserRole::STUDENT:
                return $collection->map(
                    fn($grade) => $this->studentRowData($grade, $this->user)
                );
            default:
                return new Collection();

        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Teacher',
            'Subject',
            'Student',
            'Class',
            'Grade',
            'Entered Date',
        ];
    }

    protected function adminRowData($grade)
    {
        return [
            'Teacher' => $grade->teacher->getFullName(),
            'Subject' => $grade->teacher->subject->name,
            'Student' => $grade->student->getFullName(),
            'Class' => $grade->student->class->name,
            'Grade' => $grade->grade,
            'Entered Date' => $grade->getCreatedAtFormated(),
        ];
    }

    protected function teacherRowData($grade, $user)
    {
        return [
            'Teacher' => $user->getFullName(),
            'Subject' => $user->subject->name,
            'Student' => $grade->student->getFullName(),
            'Class' => $grade->student->class->name,
            'Grade' => $grade->grade,
            'Entered Date' => $grade->getCreatedAtFormated(),
        ];
    }

    protected function studentRowData($grade, $user)
    {
        return [
            'Teacher' => $grade->teacher->getFullName(),
            'Subject' => $grade->teacher->subject->name,
            'Student' => $user->getFullName(),
            'Class' => $user->class->name,
            'Grade' => $grade->grade,
            'Entered Date' => $grade->getCreatedAtFormated(),
        ];
    }

    public static function mergeExcelFiles(int $tempFilesCount, string $tempFileNamePattern, string $outputFilePath, string $exportStatusId)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($outputFilePath);

        $tempFilePath = self::getTempFolderPath() . $tempFileNamePattern;

        $isFirstFile = true;

        // Define styles
        $headerStyle = (new StyleBuilder())
            ->setFontBold()
            ->setCellAlignment(CellAlignment::CENTER)
            ->build();

        $cellStyle = (new StyleBuilder())
            ->setCellAlignment(CellAlignment::CENTER)
            ->build();

        // Iterate through each temporary file
        for ($index = 1; $index <= $tempFilesCount; $index++) {
            $filePath = $tempFilePath . "-$index" . '.xlsx';

            if (self::isExportCancelled($exportStatusId)) {
                self::deleteAllFiles($tempFilePath, $tempFilesCount, $outputFilePath);
                return; // Exit
            }

            if (!file_exists($filePath)) {
                Log::info("Temp file not found: $filePath");
                continue;
            }

            // Initialize Spout XLSX reader for current input file
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($filePath);

            // Iterate through each sheet in the input file
            foreach ($reader->getSheetIterator() as $sheet) {
                $isFirstRow = true;

                // Iterate through each row in the sheet
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row for all but the first file
                    if ($isFirstFile && $isFirstRow) {
                        // Write the first row of the first file with header style
                        $writer->addRow($row->setStyle($headerStyle));
                        $isFirstRow = false;
                        $isFirstFile = false;
                    } elseif (!$isFirstFile && $isFirstRow) {
                        // Skip the header row for subsequent files
                        $isFirstRow = false;
                    } else {
                        // Write all other rows with cell style
                        $writer->addRow($row->setStyle($cellStyle));
                    }
                }
            }

            // Close the reader for the current input file
            $reader->close();

            // Delete the temporary file
            if (!unlink($filePath)) {
                Log::info("Failed to delete temp file: $filePath");
            }
        }

        // Close the writer to save the output file
        $writer->close();
    }

    protected static function isExportCancelled(string $exportStatusId)
    {
        return Cache::get($exportStatusId)['status'] === ExportStatus::CANCELLED;
    }

    protected static function deleteAllFiles(string $tempFilePath, int $tempFilesCount, string $mergedFilePath = null)
    {
        for ($index = 1; $index <= $tempFilesCount; $index++) {
            $filePath = $tempFilePath . "-$index" . '.xlsx';
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Also delete the merged file if it exists
        if ($mergedFilePath && file_exists($mergedFilePath)) {
            unlink($mergedFilePath);
        }
    }
}