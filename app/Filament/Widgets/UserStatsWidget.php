<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Role;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $adminCount = User::whereHas('role', function ($query) {
            $query->where('name', 'Admin LPPM');
        })->count();
        $dosenCount = User::whereHas('role', function ($query) {
            $query->where('name', 'Dosen');
        })->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();

        return [
            Stat::make('Total Pengguna', $totalUsers)
                ->description('Total pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Admin LPPM', $adminCount)
                ->description('Administrator sistem')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),

            Stat::make('Dosen', $dosenCount)
                ->description('Pengguna dosen')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Email Terverifikasi', $verifiedUsers)
                ->description('Dari ' . $totalUsers . ' total pengguna')
                ->descriptionIcon('heroicon-m-envelope-open')
                ->color('warning'),
        ];
    }
}
