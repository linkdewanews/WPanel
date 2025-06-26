<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'url',
        'user',
        'pass',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'pass',
    ];

    /**
     * The attributes that should be cast.
     *
     * Ini akan memberitahu Laravel untuk otomatis mengenkripsi
     * field 'pass' setiap kali data disimpan.
     */
    protected $casts = [
    // 'pass' => 'hashed', 
];
}