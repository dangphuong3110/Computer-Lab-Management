<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CreditClass;
use App\Models\Report;
use App\Models\Attendance;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'student_code',
        'class',
        'gender',
        'date_of_birth',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creditClasses()
    {
        return $this->belongsToMany(CreditClass::class, 'class_student');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
