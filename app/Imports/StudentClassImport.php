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

                $student->creditClasses()->attach($this->class_id,  ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
