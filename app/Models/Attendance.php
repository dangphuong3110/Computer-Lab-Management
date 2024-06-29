<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Computer;
use App\Models\ClassSession;

class Attendance extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function computer()
    {
        return $this->belongsTo(Computer::class, 'computer_id', 'id');
    }

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id', 'id');
    }
}
