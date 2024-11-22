<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'room_id',
        'room_name',
        'read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function janusRoom()
    {
        return $this->belongsTo(JanusRoom::class);
    }
        // Accessor for 'created_at' timestamp
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    // Accessor for 'updated_at' timestamp
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }
}
