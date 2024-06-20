<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassSession;

class Lesson extends Model
{
    use HasFactory;

    public function classSessions()
    {
        return $this->belongsToMany(ClassSession::class, 'class_session_lesson');
    }
}
