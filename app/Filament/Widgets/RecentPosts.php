<?php

namespace App\Filament\Widgets;

use App\Models\PostHistory;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPosts extends BaseWidget
{
    // Mengatur urutan widget di dashboard, angka lebih kecil akan di atas
    protected static ?int $sort = 2; 

    protected int | string | array $columnSpan = 'full'; // Agar widget ini memakai lebar penuh

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ambil data dari PostHistory, urutkan dari yang terbaru, batasi 5
                PostHistory::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y, H:i'),
                Tables\Columns\TextColumn::make('post_title')->label('Judul Postingan')->limit(30),
                Tables\Columns\TextColumn::make('site.name')->label('Situs'),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'Berhasil' => 'heroicon-o-check-circle',
                        'Gagal' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Berhasil' => 'success',
                        'Gagal' => 'danger',
                    }),
            ]);
    }
}