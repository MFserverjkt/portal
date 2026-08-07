<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'technician_id',
        'technician_name',
        'action_taken',
        'parts_replaced',
        'attachment',
    ];

    /**
     * Relasi ke User (Teknisi)
     */
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Relasi ke Ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}