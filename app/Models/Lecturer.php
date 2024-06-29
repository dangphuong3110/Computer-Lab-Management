<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CreditClass;
use App\Models\Report;

class Lecturer extends Model
{
    use HasFactory;


    protected $fillable = [
        'full_name',
        'academic_rank',
        'department',
        'faculty',
        'position',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creditClasses()
    {
        return $this->hasMany(CreditClass::class, 'lecturer_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'lecturer_id');
    }
}
