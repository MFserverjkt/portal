<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // 1. Import Trait HasRoles dari Spatie

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // 2. Aktifkan HasRoles di sini

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'branch_code',
        'branch_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}