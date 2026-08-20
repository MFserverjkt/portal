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
        'reporter_name',
        'asset_id',
        'department',
        'title',
        'description',
        'attachment',
        'priority',
        'branch_code',
        'branch_name',
        'status',
        'completed_at',
    ];

    /**
     * Cast tipe data kolom database ke instance Carbon / Datetime
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Relasi ke Pembuat Tiket (Pelapor/User/Outlet)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Aset Terkait
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Relasi ke Form Berita Acara Serah Terima (BAST)
     */
    public function bast()
    {
        return $this->hasOne(Bast::class, 'ticket_id');
    }
}