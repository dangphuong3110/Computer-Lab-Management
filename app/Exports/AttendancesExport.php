<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendancesExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    use Exportable;

    protected $class;
    protected $dates = [];

    public function __construct($class)
    {
        $this->class = $class;
        $this->generateDates();
    }

    public function generateDates()
    {
        $startDate = Carbon::parse($this->class->start_date);
        $endDate = Carbon::parse($this->class->end_date);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            foreach ($this->class->classSessions as $classSession) {
                if ($date->dayOfWeekIso + 1 == $classSession->day_of_week) {
                    $this->dates[] = $date->format('d-m-Y');
                }
            }
        }
    }

    public function collection()
    {
        $attendances = collect();
        $students = $this->class->students;

        foreach ($students as $index => $student) {
            $attendanceRow = [
                'STT' => $index + 1,
                'Mã sinh viên' => $student->student_code,
                'Họ và tên' => $student->full_name,
                'Lớp' => $student->class,
            ];

            foreach ($this->dates as $date) {
                $attendanceRow[$date] = '';
            }

            $sessionIds = $this->class->classSessions->pluck('id');

            $filteredAttendances = $student->attendances->whereIn('session_id', $sessionIds);

            foreach ($filteredAttendances as $attendance) {
                $attendanceDate = Carbon::parse($attendance->attendance_date)->format('d-m-Y');
                if (in_array($attendanceDate, $this->dates)) {
                    $attendanceRow[$attendanceDate] = 'x';
                }
            }

            $attendances->push($attendanceRow);
        }

        return $attendances;
    }

    public function headings(): array
    {
        return array_merge([
            'STT',
            'Mã sinh viên',
            'Họ và tên',
            'Lớp',
        ], array_map(function($date, $index) {
            return $index + 1;
        }, $this->dates, array_keys($this->dates)));
    }

    public function title(): string
    {
        return 'Attendances';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', 'Lớp học phần: ' . $this->class->name);
                $sheet->setCellValue('A2', 'Giảng viên: ' . $this->class->lecturer->full_name);
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');

                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A2')->getFont()->setBold(true);
                $sheet->getStyle('A4:' . $sheet->getHighestColumn() . '4')->getFont()->setBold(true);
                $sheet->getStyle('A5:' . $sheet->getHighestColumn() . '5')->getFont()->setBold(true);

                foreach (range('A', $sheet->getHighestColumn()) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }


                $sheet->getRowDimension(4)->setRowHeight(15);
                $sheet->getRowDimension(5)->setRowHeight(45);

                $sheet->insertNewRowBefore(6, 1);
                $highestColumn = $sheet->getHighestColumn();
                for ($col = 'A'; $col !== $highestColumn; ++$col) {
                    $sheet->setCellValue($col.'6', $sheet->getCell($col.'5')->getValue());
                }

                $sheet->getRowDimension(6)->setRowHeight(15);
                $sheet->getStyle('A6:' . $sheet->getHighestColumn() . '6')->getFont()->setBold(false);

                $sheet->getStyle('A6:' . 'A' . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle('B6:' . 'B' . $sheet->getHighestRow())->getAlignment()->setHorizontal('left');

                foreach (range(1, count($this->dates)) as $index) {
                    $column = Coordinate::stringFromColumnIndex($index + 4);

                    $dayCell = $column . '4';
                    $sheet->setCellValue($dayCell, $index);
                    $sheet->getStyle($dayCell)->getAlignment()->setHorizontal('center');

                    $dateCell = $column . '5';
                    $sheet->setCellValue($dateCell, Carbon::parse($this->dates[$index - 1])->format('j-M'));
                    $sheet->getStyle($dateCell)->getAlignment()->setTextRotation(90)->setVertical('center')->setHorizontal('center');
                }

                $sheet->mergeCells('A4:A5');
                $sheet->mergeCells('B4:B5');
                $sheet->mergeCells('C4:C5');
                $sheet->mergeCells('D4:D5');

                $sheet->getStyle('A4:D4')->getFont()->setBold(true);
                $sheet->getStyle('A4:D4')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A4:D4')->getAlignment()->setVertical('center');

                $noteColumn = Coordinate::stringFromColumnIndex(count($this->dates) + 5);

                $sheet->setCellValue($noteColumn . '4', 'Ghi chú');
                $sheet->mergeCells($noteColumn . '4:' . $noteColumn . '5');
                $sheet->getStyle($noteColumn . '4:' . $noteColumn . '5')->getFont()->setBold(true);
                $sheet->getStyle($noteColumn . '4:' . $noteColumn . '5')->getAlignment()->setHorizontal('center')->setVertical('center');

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];

                $sheet->getStyle('A4:' . $highestColumn . $highestRow)->applyFromArray($borderStyle);
            },
        ];
    }
}
