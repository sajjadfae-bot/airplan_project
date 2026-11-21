<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class pilot extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'license_number',
        'phone',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];
}
