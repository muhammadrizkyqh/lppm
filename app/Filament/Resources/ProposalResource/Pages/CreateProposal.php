<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Models\Proposal;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateProposal extends CreateRecord
{
    protected static string $resource = ProposalResource::class;

    public function mount(): void
    {
        // Check submission period validation
        $periodError = Proposal::validateSubmissionPeriod();
        if ($periodError && !auth()->user()->hasRole('admin')) {
            Notification::make()
                ->title('Periode Pengajuan Ditutup')
                ->body($periodError)
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(ProposalResource::getUrl('index'));
            return;
        }

        // Check user proposal limit
        if (auth()->user()->user_type === 'dosen') {
            $leaderError = Proposal::validateLeaderRules(auth()->id());
            if ($leaderError && !auth()->user()->hasRole('admin')) {
                Notification::make()
                    ->title('Batas Proposal Tercapai')
                    ->body($leaderError)
                    ->danger()
                    ->persistent()
                    ->send();

                $this->redirect(ProposalResource::getUrl('index'));
                return;
            }
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-assign current user as leader if not admin
        if (!auth()->user()->hasRole('admin')) {
            $data['user_id'] = auth()->id();
        }

        // Validate team composition
        $teamMembers = $data['teamMembers'] ?? [];
        $leaderId = $data['user_id'];

        $teamValidationError = Proposal::validateTeamComposition($leaderId, $teamMembers);
        if ($teamValidationError) {
            Notification::make()
                ->title('Validasi Tim Gagal')
                ->body($teamValidationError)
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Proposal berhasil dibuat')
            ->body('Proposal Anda telah berhasil dibuat dan dapat dilihat di daftar proposal.');
    }
}
