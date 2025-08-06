<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Proposal;
use App\Models\ResearchScheme;
use App\Models\User;
use App\Models\ResearchOutput;

class LppmStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Proposal', Proposal::count())
                ->description('Total proposal yang diajukan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Proposal Disetujui', Proposal::where('status', 'approved')->count())
                ->description('Proposal yang telah disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Skema Aktif', ResearchScheme::where('is_active', true)->count())
                ->description('Skema penelitian yang aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Dosen Peneliti', User::where('user_type', 'dosen')->where('is_active', true)->count())
                ->description('Dosen aktif terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make('Luaran Penelitian', ResearchOutput::where('verified_by_admin', true)->count())
                ->description('Luaran yang telah diverifikasi')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            Stat::make('Target Publikasi', '40')
                ->description('Target publikasi tahunan')
                ->descriptionIcon('heroicon-m-flag')
                ->color('gray'),
        ];
    }
}
