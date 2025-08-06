<?php

namespace App\Filament\Resources\ProposalTeamMemberResource\Pages;

use App\Filament\Resources\ProposalTeamMemberResource;
use App\Models\ProposalTeamMember;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditProposalTeamMember extends EditRecord
{
    protected static string $resource = ProposalTeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // Cek apakah yang akan dihapus adalah ketua tim
                    if ($this->record->role === 'ketua') {
                        $memberCount = ProposalTeamMember::where('proposal_id', $this->record->proposal_id)
                            ->where('id', '!=', $this->record->id)
                            ->count();

                        if ($memberCount > 0) {
                            Notification::make()
                                ->title('Tidak dapat menghapus ketua tim')
                                ->body('Hapus anggota tim lainnya terlebih dahulu sebelum menghapus ketua')
                                ->danger()
                                ->send();

                            return false;
                        }
                    }

                    return true;
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $data = $this->data;
        $originalRecord = $this->record;

        // Validasi jika mengubah role menjadi ketua
        if ($data['role'] === 'ketua' && $originalRecord->role !== 'ketua') {
            $existingLeader = ProposalTeamMember::where('proposal_id', $data['proposal_id'])
                ->where('role', 'ketua')
                ->where('id', '!=', $originalRecord->id)
                ->exists();

            if ($existingLeader) {
                Notification::make()
                    ->title('Gagal mengubah role')
                    ->body('Sudah ada ketua tim untuk proposal ini')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        // Validasi jika mengubah user_id
        if ($data['user_id'] !== $originalRecord->user_id) {
            $existingMember = ProposalTeamMember::where('proposal_id', $data['proposal_id'])
                ->where('user_id', $data['user_id'])
                ->where('id', '!=', $originalRecord->id)
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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Anggota tim berhasil diperbarui';
    }
}
