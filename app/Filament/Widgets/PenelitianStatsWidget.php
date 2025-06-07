<?php

namespace App\Filament\Widgets;

use App\Models\Penelitian;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PenelitianStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPenelitian = Penelitian::count();
        $menunggu = Penelitian::where('status', 'menunggu')->count();
        $disetujui = Penelitian::where('status', 'disetujui')->count();
        $ditolak = Penelitian::where('status', 'ditolak')->count();

        $totalDana = Penelitian::where('status', 'disetujui')->sum('dana_diajukan');

        return [
            Stat::make('Total Penelitian', $totalPenelitian)
                ->description('Semua pengajuan penelitian')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Menunggu Verifikasi', $menunggu)
                ->description('Perlu ditinjau')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Disetujui', $disetujui)
                ->description('Penelitian yang disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Dana Disetujui', 'Rp ' . number_format($totalDana, 0, ',', '.'))
                ->description('Dana penelitian yang disetujui')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
