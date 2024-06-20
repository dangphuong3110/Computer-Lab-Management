<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Building;
use App\Models\ClassSession;
use App\Models\Computer;

class Room extends Model
{
    use HasFactory;

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function computers()
    {
        return $this->hasMany(Computer::class);
    }
}
