<?php

namespace App\Exports;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class GradesExport implements FromCollection, WithHeadings
{

    // protected $filters;
    // protected $gradeRepository;
    protected $coll;

    public function __construct($coll)
    {
        $this->coll = $coll;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $collection = $this->coll;

        /** @var \App\Models\User $user */
        $user = auth()->user();

        switch ($user->getRoleId()) {
            case UserRole::ADMIN:
                return $collection->map(
                    fn($grade) => $this->adminRowData($grade)
                );
            case UserRole::TEACHER:
                return $collection->map(
                    fn($grade) => $this->teacherRowData($grade, $user)
                );
            case UserRole::STUDENT:
                return $collection->map(
                    fn($grade) => $this->studentRowData($grade, $user)
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
            'Grade',
            'Created At',
        ];
    }

    protected function adminRowData($grade)
    {
        return [
            'Teacher' => $grade->teacher->fullname,
            'Subject' => $grade->teacher->subject->name,
            'Student' => $grade->student->fullname,
            'Grade' => $grade->grade,
            'Created At' => $grade->created_at->format('m/d/Y'),
        ];
    }

    protected function teacherRowData($grade, $user)
    {
        return [
            'Teacher' => $user->fullname,
            'Subject' => $user->subject->name,
            'Student' => $grade->student->fullname,
            'Grade' => $grade->grade,
            'Created At' => $grade->created_at->format('m/d/Y'),
        ];
    }

    protected function studentRowData($grade, $user)
    {
        return [
            'Teacher' => $grade->teacher->fullname,
            'Subject' => $grade->teacher->subject->name,
            'Student' => $user->fullname,
            'Grade' => $grade->grade,
            'Created At' => $grade->created_at->format('m/d/Y'),
        ];
    }


    static function mergeExcelFiles(int $tempFilesCount, string $tempFileNamePattern, string $outputFilePath)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($outputFilePath);

        $tempFilePath = storage_path('app/public') . $tempFileNamePattern;
        // Iterate through each temporary file
        for ($index = 1; $index <= $tempFilesCount; $index++) {
            $filePath = $tempFilePath . "-$index" . '.xlsx';

            // Initialize Spout XLSX reader for current input file
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($filePath);

            // Iterate through each sheet in the input file
            foreach ($reader->getSheetIterator() as $sheet) {
                // Iterate through each row in the sheet
                foreach ($sheet->getRowIterator() as $row) {
                    // Add row to the output file
                    $writer->addRow($row);
                }
            }

            // Close the reader for the current input file
            $reader->close();

            // Delete the temporary file
            unlink($filePath);
        }

        // Close the writer to save the output file
        $writer->close();
    }
}