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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creditClasses()
    {
        return $this->hasMany(CreditClass::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
