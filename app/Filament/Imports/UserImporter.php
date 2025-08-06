<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping()
                ->rules(['required', 'email']),

            ImportColumn::make('nidn')
                ->label('NIDN')
                ->rules(['nullable', 'max:20']),

            ImportColumn::make('phone')
                ->label('No. Telepon')
                ->rules(['nullable', 'max:20']),

            ImportColumn::make('user_type')
                ->label('Jenis User')
                ->rules(['required', 'in:dosen,admin,reviewer,pimpinan']),

            ImportColumn::make('employment_status')
                ->label('Status Kepegawaian')
                ->rules(['required', 'in:tetap,kontrak,tidak_tetap,honorer']),

            ImportColumn::make('functional_position')
                ->label('Jabatan Fungsional')
                ->rules(['nullable', 'in:asisten_ahli,lektor,lektor_kepala,guru_besar,profesor,tenaga_pengajar,tidak_ada']),

            ImportColumn::make('field_of_study')
                ->label('Bidang Keahlian')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('address')
                ->label('Alamat')
                ->rules(['nullable']),

            ImportColumn::make('is_active')
                ->label('Status Aktif')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?User
    {
        // Check if user exists by email first
        $existingUser = User::where('email', $this->data['email'])->first();

        if ($existingUser) {
            return $existingUser; // Update existing user
        }

        // Check by NIDN if provided and not empty
        if (!empty($this->data['nidn'])) {
            $existingByNidn = User::where('nidn', $this->data['nidn'])->first();
            if ($existingByNidn) {
                return $existingByNidn; // Update existing user by NIDN
            }
        }

        // Create new user
        return new User();
    }

    protected function beforeSave(): void
    {
        // Set default password if creating new user
        if (!$this->record->exists) {
            $this->record->password = Hash::make('password123');
        }

        // Set default values
        if (empty($this->record->user_type)) {
            $this->record->user_type = 'dosen';
        }

        if (empty($this->record->employment_status)) {
            $this->record->employment_status = 'tetap';
        }

        if (!isset($this->record->is_active)) {
            $this->record->is_active = true;
        }

        // Convert is_active from string to boolean if needed
        if (isset($this->data['is_active'])) {
            $this->record->is_active = in_array($this->data['is_active'], ['1', 'true', true, 1], true);
        }
    }

    protected function afterSave(): void
    {
        // Assign default role if user doesn't have any role
        if (!$this->record->hasAnyRole(['admin', 'dosen', 'reviewer', 'pimpinan'])) {
            $this->record->assignRole('dosen');
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data dosen telah selesai dan ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimport.';
        }

        return $body;
    }
}
