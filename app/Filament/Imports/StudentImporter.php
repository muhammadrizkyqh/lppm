<?php

namespace App\Filament\Imports;

use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('nim')
                ->label('NIM')
                ->requiredMapping()
                ->rules(['required', 'max:20', 'unique:students,nim']),

            ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping()
                ->rules(['required', 'email', 'unique:students,email']),

            ImportColumn::make('phone')
                ->label('No. Telepon')
                ->rules(['nullable', 'max:20']),

            ImportColumn::make('education_level')
                ->label('Jenjang Pendidikan')
                ->requiredMapping()
                ->rules(['required', 'in:S1,S2,S3']),

            ImportColumn::make('study_program')
                ->label('Program Studi')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('department')
                ->label('Fakultas/Jurusan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('semester')
                ->label('Semester')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:1', 'max:14']),

            ImportColumn::make('entry_year')
                ->label('Tahun Masuk')
                ->numeric()
                ->rules(['required', 'integer', 'min:2000', 'max:' . date('Y')]),

            ImportColumn::make('gpa')
                ->label('IPK')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:4']),

            ImportColumn::make('address')
                ->label('Alamat')
                ->rules(['nullable']),

            ImportColumn::make('is_active')
                ->label('Status Aktif')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?Student
    {
        return Student::firstOrNew([
            'nim' => $this->data['nim'],
        ]);
    }

    protected function beforeSave(): void
    {
        // Set default values
        if (empty($this->record->entry_year)) {
            $this->record->entry_year = date('Y');
        }

        if (!isset($this->record->is_active)) {
            $this->record->is_active = true;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data mahasiswa telah selesai dan ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimport.';
        }

        return $body;
    }
}
