<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CreditClass;
use App\Models\Room;
use App\Models\Lesson;
use App\Models\Attendance;

class ClassSession extends Model
{
    use HasFactory;

    public function creditClass()
    {
        return $this->belongsTo(CreditClass::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'class_session_lesson', 'session_id', 'lesson_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
