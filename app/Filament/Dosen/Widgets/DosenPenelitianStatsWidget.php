<?php

namespace App\Filament\Dosen\Widgets;

use App\Models\Penelitian;
use App\Models\LuaranPenelitian;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;

class DosenPenelitianStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Filament::auth()->id();

        $totalPenelitian = Penelitian::where('user_id', $userId)->count();
        $menunggu = Penelitian::where('user_id', $userId)->where('status', 'menunggu')->count();
        $disetujui = Penelitian::where('user_id', $userId)->where('status', 'disetujui')->count();

        $totalLuaran = LuaranPenelitian::whereHas('penelitian', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        return [
            Stat::make('Total Penelitian Saya', $totalPenelitian)
                ->description('Semua penelitian yang diajukan')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Menunggu Verifikasi', $menunggu)
                ->description('Sedang dalam proses review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Penelitian Disetujui', $disetujui)
                ->description('Penelitian yang telah disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Luaran', $totalLuaran)
                ->description('Luaran penelitian yang dipublikasi')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
        ];
    }
}
