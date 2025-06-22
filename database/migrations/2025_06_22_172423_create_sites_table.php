<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('user');
            $table->text('pass'); // Menggunakan 'text' agar cukup untuk password terenkripsi
            $table->timestamps(); // Otomatis membuat kolom created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};