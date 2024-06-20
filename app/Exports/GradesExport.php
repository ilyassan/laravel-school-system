<?php

namespace App\Exports;

use App\Enums\UserRole;
use App\Repositories\GradeRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class GradesExport implements FromCollection, WithHeadings
{

    // protected $filters;
    // protected $gradeRepository;
    protected $coll;

    public function __construct($coll)
    {
        // $this->filters = $filters;
        // $this->gradeRepository = app()->make(GradeRepository::class);
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

    static function mergeExcelFiles(array $filePaths, string $outputPath)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;
        foreach ($filePaths as $index => $filePath) {
            $tempSpreadsheet = IOFactory::load($filePath);
            $tempSheet = $tempSpreadsheet->getActiveSheet();
            $startRow = ($index === 0) ? 1 : 2; // Skip header row for all but the first file

            foreach ($tempSheet->getRowIterator($startRow) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $colIndex = 1;
                foreach ($cellIterator as $cell) {
                    $sheet->setCellValue([$colIndex, $rowIndex], $cell->getValue());
                    $colIndex++;
                }
                $rowIndex++;
            }

            // Delete the temporary file
            unlink($filePath);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);
    }
}
