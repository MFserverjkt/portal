<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'asset_id',
        'department',
        'title',
        'description',
        'priority',
        'status',
    ];

    // Relasi ke Pembuat Tiket (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Aset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi ke Form BAST
    public function bast()
    {
        return $this->hasOne(Bast::class);
    }
}