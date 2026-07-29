<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'asset_name',
        'category',
        'brand',
        'branch_code',
        'branch_name',
        'register_date',
        'status',
        'description',
    ];
}