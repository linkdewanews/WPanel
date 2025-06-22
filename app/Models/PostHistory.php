<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostHistory extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan semua field diisi

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}