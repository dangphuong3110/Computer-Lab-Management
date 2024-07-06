<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use App\Models\Attendance;

class Computer extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'configuration',
        'purchase_date',
        'status',
        'room_id',
        'is_active',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'computer_id', 'id');
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class, 'computer_id', 'id');
    }
}
