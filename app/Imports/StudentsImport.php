<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $rules = [
            'ho_va_ten' => 'required|string|max:255',
            'ma_sinh_vien' => 'required|string|max:255',
        ];

        foreach ($rows as $row)
        {
            $trimmedRow = array_map('trim', $row->toArray());

            if (!empty($trimmedRow['ngay_sinh'])) {
                $trimmedRow['ngay_sinh'] = Carbon::createFromFormat('d-m-Y', $trimmedRow['ngay_sinh'])->format('Y-m-d');
            }

            $validator = Validator::make($trimmedRow, $rules);

            if ($validator->fails()) {
                continue;
            }

            $user = User::where('email', $trimmedRow['ma_sinh_vien'])->first();

            if (!$user) {
                $user = User::create([
                    'email' => $trimmedRow['ma_sinh_vien'],
                    'password' => Hash::make('123456'),
                    'phone' => $trimmedRow['so_dien_thoai'],
                    'role_id' => 3,
                ]);
                Student::create([
                    'full_name' => $trimmedRow['ho_va_ten'],
                    'student_code' => $trimmedRow['ma_sinh_vien'],
                    'class' => $trimmedRow['lop'],
                    'gender' => $trimmedRow['gioi_tinh'],
                    'date_of_birth' => $trimmedRow['ngay_sinh'],
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
