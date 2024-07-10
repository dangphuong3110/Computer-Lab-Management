<?php

namespace App\Imports;

use App\Models\CreditClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentClassImport implements ToCollection, WithHeadingRow
{
    protected string $class_id;

    public function __construct(string $class_id)
    {
        $this->class_id = $class_id;
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $rules = [
            'ma_sinh_vien' => 'required|string|max:255',
        ];

        foreach ($rows as $row)
        {
            $trimmedRow = array_map('trim', $row->toArray());

            $validator = Validator::make($trimmedRow, $rules);

            if ($validator->fails()) {
                continue;
            }

            $student = Student::where('student_code', $trimmedRow['ma_sinh_vien'])->first();

            if (!$student) {
                continue;
            } else {
                if ($student->creditClasses()->where('id', $this->class_id)->exists()) {
                    continue;
                }

                $newClass = CreditClass::findOrFail($this->class_id);
                $newClassSessions = $newClass->classSessions;
                $studentsCount = $newClass->students()->count();
                $minRoomCapacity = $newClassSessions->pluck('room.capacity')->min();

                if ($studentsCount == $minRoomCapacity) {
                    continue;
                }

                $currentClasses = $student->creditClasses;

                $isOverlapping = false;
                foreach ($currentClasses as $currentClass) {
                    if (Carbon::parse($newClass->start_date)->between($currentClass->start_date, $currentClass->end_date) ||
                        Carbon::parse($newClass->end_date)->between($currentClass->start_date, $currentClass->end_date)) {
                        $currentClassSessions = $currentClass->classSessions;

                        foreach ($newClassSessions as $newClassSession) {
                            foreach ($currentClassSessions as $currentClassSession) {
                                if ($newClassSession->day_of_week == $currentClassSession->day_of_week &&
                                    $newClassSession->start_lesson <= $currentClassSession->end_lesson &&
                                    $newClassSession->end_lesson >= $currentClassSession->start_lesson) {
                                    $isOverlapping = true;
                                    break 3;
                                }
                            }
                        }
                    }
                }

                if ($isOverlapping) {
                    continue;
                }

                $student->creditClasses()->attach($this->class_id,  ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
