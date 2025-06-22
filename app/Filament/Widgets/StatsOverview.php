<?php

namespace App\Filament\Widgets;

use App\Models\Site;
use App\Models\PostHistory;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
{
    return [
        // Kartu Statistik 1: Jumlah Situs
        Stat::make('Total Situs', Site::count())
            ->description('Jumlah semua situs yang terdaftar')
            ->descriptionIcon('heroicon-m-globe-alt')
            ->color('primary'),

        // Kartu Statistik 2: Postingan Berhasil
        Stat::make('Postingan Berhasil', PostHistory::where('status', 'Berhasil')->count())
            ->description('Total postingan yang sukses terkirim')
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('success'),

        // Kartu Statistik 3: Postingan Gagal
        Stat::make('Postingan Gagal', PostHistory::where('status', 'Gagal')->count())
            ->description('Total postingan yang gagal terkirim')
            ->descriptionIcon('heroicon-m-x-circle')
            ->color('danger'),
    ];
}
}
