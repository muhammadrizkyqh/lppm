<?php

namespace App\Filament\Resources\ProposalTeamMemberResource\Pages;

use App\Filament\Resources\ProposalTeamMemberResource;
use App\Models\ProposalTeamMember;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProposalTeamMembers extends ListRecords
{
    protected static string $resource = ProposalTeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Anggota Tim')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(fn () => ProposalTeamMember::count()),

            'ketua' => Tab::make('Ketua Tim')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'ketua'))
                ->badge(fn () => ProposalTeamMember::where('role', 'ketua')->count()),

            'anggota' => Tab::make('Anggota')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'anggota'))
                ->badge(fn () => ProposalTeamMember::where('role', 'anggota')->count()),

            'dosen' => Tab::make('Dosen')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('member_type', 'dosen'))
                ->badge(fn () => ProposalTeamMember::where('member_type', 'dosen')->count()),

            'mahasiswa' => Tab::make('Mahasiswa')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('member_type', 'mahasiswa'))
                ->badge(fn () => ProposalTeamMember::where('member_type', 'mahasiswa')->count()),
        ];
    }
}
