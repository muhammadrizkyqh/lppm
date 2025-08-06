<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Filament\Imports\StudentImporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Mahasiswa';
    protected static ?string $modelLabel = 'Mahasiswa';
    protected static ?string $pluralModelLabel = 'Mahasiswa';
    protected static ?string $navigationGroup = 'MASTER DATA';
    protected static ?int $navigationSort = 3;

    // Permission checking - Admin only
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('manage_users');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('manage_users');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('manage_users');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('manage_users');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('manage_users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nim')
                            ->label('NIM')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Data Akademik')
                    ->schema([
                        Forms\Components\Select::make('education_level')
                            ->label('Jenjang Pendidikan')
                            ->options([
                                'S1' => 'Sarjana (S1)',
                                'S2' => 'Magister (S2)',
                                'S3' => 'Doktor (S3)',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('study_program')
                            ->label('Program Studi')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('department')
                            ->label('Fakultas/Jurusan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('semester')
                            ->label('Semester')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(14),

                        Forms\Components\TextInput::make('entry_year')
                            ->label('Tahun Masuk')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(date('Y'))
                            ->default(date('Y'))
                            ->required(),

                        Forms\Components\TextInput::make('gpa')
                            ->label('IPK')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(4)
                            ->step(0.01),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Lainnya')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('education_level')
                    ->label('Jenjang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'S1' => 'success',
                        'S2' => 'warning',
                        'S3' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('study_program')
                    ->label('Program Studi')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable(),

                Tables\Columns\TextColumn::make('entry_year')
                    ->label('Tahun Masuk')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gpa')
                    ->label('IPK')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('education_level')
                    ->label('Jenjang Pendidikan')
                    ->options([
                        'S1' => 'Sarjana (S1)',
                        'S2' => 'Magister (S2)',
                        'S3' => 'Doktor (S3)',
                    ]),

                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),

                Tables\Filters\Filter::make('entry_year')
                    ->form([
                        Forms\Components\Select::make('year')
                            ->label('Tahun Masuk')
                            ->options(array_combine(
                                range(date('Y'), 2000, -1),
                                range(date('Y'), 2000, -1)
                            ))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['year'],
                            fn (Builder $query, $year): Builder => $query->where('entry_year', $year),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->label('Import Data Mahasiswa')
                    ->importer(StudentImporter::class)
                    ->color('success')
                    ->icon('heroicon-o-arrow-up-tray'),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Mahasiswa'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
