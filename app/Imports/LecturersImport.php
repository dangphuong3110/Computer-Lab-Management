<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LecturersImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Lecturer::create([
                'full_name' => $row['Họ và tên'],
                'academic_rank' => $row['Học vị'],
                'department' => $row['Bộ môn'],
                'faculty' => $row['Khoa'],
                'position' => $row['Chức vị'],
            ]);

            User::create([
                'email' => $row['Email'],
                'password' => Hash::make('123456'),
                'role_id' => 4,
            ]);
        }
    }
//    public function rules(): array
//    {
//        return [
//            '*.Họ và tên' => 'required|string|max:255',
//            '*.Email' => 'required|email|unique:users,email',
//        ];
//    }
}
