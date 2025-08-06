<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\LppmStatsWidget;
use App\Filament\Widgets\ProposalProgressChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard LPPM';
    protected static ?int $navigationSort = -1;

    public function getWidgets(): array
    {
        return [
            LppmStatsWidget::class,
            ProposalProgressChart::class,
            AccountWidget::class,
        ];
    }
}
