<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\ClassSession;

class CreditClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_class');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }
}
