<?php

namespace App\Filament\Resources\ProposalTeamMemberResource\Pages;

use App\Filament\Resources\ProposalTeamMemberResource;
use App\Models\ProposalTeamMember;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateProposalTeamMember extends CreateRecord
{
    protected static string $resource = ProposalTeamMemberResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->data;

        // Validasi maksimal 4 anggota per proposal (termasuk ketua)
        $currentMemberCount = ProposalTeamMember::where('proposal_id', $data['proposal_id'])->count();

        if ($currentMemberCount >= 4) {
            Notification::make()
                ->title('Gagal menambah anggota tim')
                ->body('Maksimal 4 orang dalam satu tim (1 ketua + 3 anggota)')
                ->danger()
                ->send();

            $this->halt();
        }

        // Validasi hanya boleh 1 ketua per proposal
        if ($data['role'] === 'ketua') {
            $existingLeader = ProposalTeamMember::where('proposal_id', $data['proposal_id'])
                ->where('role', 'ketua')
                ->exists();

            if ($existingLeader) {
                Notification::make()
                    ->title('Gagal menambah ketua tim')
                    ->body('Sudah ada ketua tim untuk proposal ini')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        // Validasi tidak boleh duplikasi anggota yang sama
        if ($data['user_id']) {
            $existingMember = ProposalTeamMember::where('proposal_id', $data['proposal_id'])
                ->where('user_id', $data['user_id'])
                ->exists();

            if ($existingMember) {
                Notification::make()
                    ->title('Anggota sudah ada')
                    ->body('Dosen ini sudah menjadi anggota tim proposal')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Anggota tim berhasil ditambahkan';
    }
}
