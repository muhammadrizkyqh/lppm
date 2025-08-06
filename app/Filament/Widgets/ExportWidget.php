<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Proposal;
use App\Models\Contract;
use App\Models\Seminar;
use App\Models\PublicationFunding;
use App\Models\User;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Response;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportWidget extends Widget
{
    protected static string $view = 'filament.widgets.export-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 10;

    public function getActions(): array
    {
        return [
            Action::make('export_proposals_pdf')
                ->label('Export Laporan Proposal (PDF)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    return $this->exportProposalsPDF();
                }),

            Action::make('export_contracts_pdf')
                ->label('Export Laporan Kontrak (PDF)')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->action(function () {
                    return $this->exportContractsPDF();
                }),

            Action::make('export_seminars_pdf')
                ->label('Export Laporan Seminar (PDF)')
                ->icon('heroicon-o-presentation-chart-line')
                ->color('info')
                ->action(function () {
                    return $this->exportSeminarsPDF();
                }),

            Action::make('export_publications_pdf')
                ->label('Export Laporan Publikasi (PDF)')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->action(function () {
                    return $this->exportPublicationsPDF();
                }),

            Action::make('export_comprehensive_report')
                ->label('Export Laporan Komprehensif (PDF)')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->action(function () {
                    return $this->exportComprehensiveReport();
                }),
        ];
    }

    protected function exportProposalsPDF()
    {
        $proposals = Proposal::with(['user', 'researchScheme', 'reviews'])
            ->orderBy('created_at', 'desc')
            ->get();

        $html = $this->generateProposalsHTML($proposals);
        return $this->generatePDF($html, 'Laporan_Proposal_' . now()->format('Y-m-d'));
    }

    protected function exportContractsPDF()
    {
        $contracts = Contract::with(['proposal', 'secondParty'])
            ->orderBy('created_at', 'desc')
            ->get();

        $html = $this->generateContractsHTML($contracts);
        return $this->generatePDF($html, 'Laporan_Kontrak_' . now()->format('Y-m-d'));
    }

    protected function exportSeminarsPDF()
    {
        $seminars = Seminar::with(['proposal', 'scheduledBy'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $html = $this->generateSeminarsHTML($seminars);
        return $this->generatePDF($html, 'Laporan_Seminar_' . now()->format('Y-m-d'));
    }

    protected function exportPublicationsPDF()
    {
        $publications = PublicationFunding::with(['proposal', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $html = $this->generatePublicationsHTML($publications);
        return $this->generatePDF($html, 'Laporan_Publikasi_' . now()->format('Y-m-d'));
    }

    protected function exportComprehensiveReport()
    {
        $data = [
            'proposals' => Proposal::with(['user', 'researchScheme'])->get(),
            'contracts' => Contract::with(['proposal', 'secondParty'])->get(),
            'seminars' => Seminar::with(['proposal'])->get(),
            'publications' => PublicationFunding::with(['proposal', 'user'])->get(),
            'statistics' => $this->getStatistics(),
        ];

        $html = $this->generateComprehensiveHTML($data);
        return $this->generatePDF($html, 'Laporan_Komprehensif_LPPM_' . now()->format('Y-m-d'));
    }

    protected function generateProposalsHTML($proposals)
    {
        $html = '
        <html>
        <head>
            <title>Laporan Proposal Penelitian</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 30px; }
                .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .table th { background-color: #f2f2f2; font-weight: bold; }
                .status { padding: 3px 6px; border-radius: 3px; color: white; font-size: 10px; }
                .status-draft { background-color: #6b7280; }
                .status-submitted { background-color: #f59e0b; }
                .status-approved { background-color: #10b981; }
                .status-rejected { background-color: #ef4444; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN PROPOSAL PENELITIAN</h1>
                <h2>LPPM STAI Ali bin Abi Thalib Surabaya</h2>
                <p>Periode: ' . now()->format('d F Y') . '</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Proposal</th>
                        <th>Peneliti</th>
                        <th>Skema</th>
                        <th>Dana</th>
                        <th>Status</th>
                        <th>Tanggal Submit</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($proposals as $index => $proposal) {
            $statusClass = 'status-' . str_replace('_', '-', $proposal->status);
            $statusLabel = ucfirst(str_replace('_', ' ', $proposal->status));

            $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $proposal->title . '</td>
                        <td>' . $proposal->user->name . '</td>
                        <td>' . $proposal->researchScheme->name . '</td>
                        <td>Rp ' . number_format($proposal->fund_amount, 0, ',', '.') . '</td>
                        <td><span class="status ' . $statusClass . '">' . $statusLabel . '</span></td>
                        <td>' . ($proposal->submitted_at ? $proposal->submitted_at->format('d/m/Y') : '-') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div style="margin-top: 30px;">
                <h3>Ringkasan Statistik</h3>
                <p>Total Proposal: ' . $proposals->count() . '</p>
                <p>Proposal Disetujui: ' . $proposals->where('status', 'approved')->count() . '</p>
                <p>Proposal Ditolak: ' . $proposals->where('status', 'rejected')->count() . '</p>
                <p>Total Dana Disetujui: Rp ' . number_format($proposals->where('status', 'approved')->sum('fund_amount'), 0, ',', '.') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    protected function generateContractsHTML($contracts)
    {
        $html = '
        <html>
        <head>
            <title>Laporan Kontrak Penelitian</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 30px; }
                .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .table th { background-color: #f2f2f2; font-weight: bold; }
                .status { padding: 3px 6px; border-radius: 3px; color: white; font-size: 10px; }
                .status-active { background-color: #10b981; }
                .status-signed { background-color: #3b82f6; }
                .status-completed { background-color: #6366f1; }
                .status-draft { background-color: #6b7280; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN KONTRAK PENELITIAN</h1>
                <h2>LPPM STAI Ali bin Abi Thalib Surabaya</h2>
                <p>Periode: ' . now()->format('d F Y') . '</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Kontrak</th>
                        <th>Judul</th>
                        <th>Peneliti</th>
                        <th>Nilai Kontrak</th>
                        <th>Periode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($contracts as $index => $contract) {
            $statusClass = 'status-' . str_replace('_', '-', $contract->status);
            $statusLabel = ucfirst(str_replace('_', ' ', $contract->status));

            $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $contract->contract_number . '</td>
                        <td>' . $contract->title . '</td>
                        <td>' . $contract->secondParty->name . '</td>
                        <td>Rp ' . number_format($contract->contract_value, 0, ',', '.') . '</td>
                        <td>' . $contract->start_date->format('d/m/Y') . ' - ' . $contract->end_date->format('d/m/Y') . '</td>
                        <td><span class="status ' . $statusClass . '">' . $statusLabel . '</span></td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div style="margin-top: 30px;">
                <h3>Ringkasan Statistik</h3>
                <p>Total Kontrak: ' . $contracts->count() . '</p>
                <p>Kontrak Aktif: ' . $contracts->where('status', 'active')->count() . '</p>
                <p>Kontrak Selesai: ' . $contracts->where('status', 'completed')->count() . '</p>
                <p>Total Nilai Kontrak: Rp ' . number_format($contracts->sum('contract_value'), 0, ',', '.') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    protected function generateSeminarsHTML($seminars)
    {
        // Similar structure for seminars...
        return '<html><body><h1>Laporan Seminar - Coming Soon</h1></body></html>';
    }

    protected function generatePublicationsHTML($publications)
    {
        // Similar structure for publications...
        return '<html><body><h1>Laporan Publikasi - Coming Soon</h1></body></html>';
    }

    protected function generateComprehensiveHTML($data)
    {
        $html = '
        <html>
        <head>
            <title>Laporan Komprehensif LPPM</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 30px; }
                .section { margin-bottom: 40px; page-break-inside: avoid; }
                .stats-grid { display: flex; justify-content: space-between; margin-bottom: 20px; }
                .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN KOMPREHENSIF</h1>
                <h2>LPPM STAI Ali bin Abi Thalib Surabaya</h2>
                <p>Periode: ' . now()->format('d F Y') . '</p>
            </div>

            <div class="section">
                <h2>Ringkasan Eksekutif</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>' . $data['proposals']->count() . '</h3>
                        <p>Total Proposal</p>
                    </div>
                    <div class="stat-card">
                        <h3>' . $data['contracts']->count() . '</h3>
                        <p>Total Kontrak</p>
                    </div>
                    <div class="stat-card">
                        <h3>' . $data['seminars']->count() . '</h3>
                        <p>Total Seminar</p>
                    </div>
                    <div class="stat-card">
                        <h3>' . $data['publications']->count() . '</h3>
                        <p>Total Publikasi</p>
                    </div>
                </div>
            </div>

            <p style="text-align: center; margin-top: 50px; font-style: italic;">
                Laporan ini dibuat secara otomatis oleh Sistem LPPM STAI Ali bin Abi Thalib Surabaya
            </p>
        </body>
        </html>';

        return $html;
    }

    protected function generatePDF($html, $filename)
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return Response::streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function getStatistics()
    {
        return [
            'total_proposals' => Proposal::count(),
            'approved_proposals' => Proposal::where('status', 'approved')->count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'completed_seminars' => Seminar::where('status', 'completed')->count(),
            'published_papers' => PublicationFunding::where('status', 'published')->count(),
        ];
    }
}
