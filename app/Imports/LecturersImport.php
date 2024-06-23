<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturersImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $rules = [
            'ho_va_ten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ];

        foreach ($rows as $row)
        {
            $trimmedRow = array_map('trim', $row->toArray());


            $validator = Validator::make($trimmedRow, $rules);

            if ($validator->fails()) {
                continue;
            }

            $user = User::where('email', $trimmedRow['email'])->first();

            if (!$user) {
                $user = User::create([
                    'email' => $trimmedRow['email'],
                    'password' => Hash::make('123456'),
                    'phone' => $trimmedRow['so_dien_thoai'],
                    'role_id' => 4,
                ]);
                Lecturer::create([
                    'full_name' => $trimmedRow['ho_va_ten'],
                    'academic_rank' => $trimmedRow['hoc_vi'],
                    'department' => $trimmedRow['bo_mon'],
                    'faculty' => $trimmedRow['khoa'],
                    'position' => $trimmedRow['chuc_vi'],
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
