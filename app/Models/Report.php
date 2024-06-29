<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Technician;

class Report extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id', 'id');
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class, 'technician_id', 'id');
    }
}
