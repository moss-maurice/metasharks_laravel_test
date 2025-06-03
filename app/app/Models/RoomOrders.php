<?php

namespace App\Models;

use App\Models\Rooms;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomOrders extends Model
{
    /** @use HasFactory<\Database\Factories\RoomOrderFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'room_id', 'date'];

    public function room()
    {
        return $this->belongsTo(Rooms::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
