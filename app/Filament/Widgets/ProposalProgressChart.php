<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Proposal;

class ProposalProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Status Proposal';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statusCounts = [
            'draft' => Proposal::where('status', 'draft')->count(),
            'submitted' => Proposal::where('status', 'submitted')->count(),
            'under_review' => Proposal::where('status', 'under_review')->count(),
            'revision_required' => Proposal::where('status', 'revision_required')->count(),
            'approved' => Proposal::where('status', 'approved')->count(),
            'rejected' => Proposal::where('status', 'rejected')->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Status Proposal',
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        '#9CA3AF', // gray - draft
                        '#F59E0B', // amber - submitted
                        '#3B82F6', // blue - under_review
                        '#EF4444', // red - revision_required
                        '#10B981', // green - approved
                        '#DC2626', // red - rejected
                    ],
                    'borderWidth' => 2,
                    'borderColor' => '#fff',
                ],
            ],
            'labels' => [
                'Draft',
                'Diajukan',
                'Sedang Review',
                'Perlu Revisi',
                'Disetujui',
                'Ditolak',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
