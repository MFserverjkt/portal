<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
    protected $fillable = [
        'ticket_id', 'technician_id', 'action_taken', 'parts_replaced', 'completed_at'
    ];

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}