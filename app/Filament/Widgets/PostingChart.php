<?php

namespace App\Filament\Widgets;

use App\Models\PostHistory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PostingChart extends ChartWidget
{
    protected static ?string $heading = 'Aktivitas Postingan (7 Hari Terakhir)';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // Ambil data postingan berhasil dalam 7 hari terakhir
        $data = PostHistory::query()
            ->where('status', 'Berhasil')
            ->where('created_at', '>=', now()->subDays(7))
            ->get()
            ->groupBy(fn ($date) => Carbon::parse($date->created_at)->format('Y-m-d'))
            ->map(fn ($group) => $group->count());

        // Siapkan label untuk 7 hari ke belakang
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('D, d M');
        }

        // Siapkan data chart, isi dengan 0 jika di hari itu tidak ada postingan
        $chartData = [];
        foreach ($labels as $label) {
            $dateKey = Carbon::createFromFormat('D, d M', $label)->format('Y-m-d');
            $chartData[] = $data->get($dateKey, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Postingan Berhasil',
                    'data' => $chartData,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Tipe grafik: garis
    }
}