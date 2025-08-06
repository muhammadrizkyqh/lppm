<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DosenResource\Pages;
use App\Filament\Resources\DosenResource\RelationManagers;
use App\Models\User;
use App\Filament\Imports\UserImporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DosenResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Dosen';
    protected static ?string $modelLabel = 'Dosen';
    protected static ?string $pluralModelLabel = 'Dosen';
    protected static ?string $navigationGroup = 'MASTER DATA';
    protected static ?int $navigationSort = 2;

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

    // Only show dosen type users
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_type', 'dosen');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

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

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Akademik')
                    ->schema([
                        Forms\Components\TextInput::make('nidn')
                            ->label('NIDN')
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\Hidden::make('user_type')
                            ->default('dosen'),

                        Forms\Components\Select::make('employment_status')
                            ->label('Status Kepegawaian')
                            ->options([
                                'tetap' => 'Dosen Tetap',
                                'kontrak' => 'Dosen Kontrak',
                                'honorer' => 'Dosen Honorer',
                                'luar_biasa' => 'Dosen Luar Biasa',
                            ])
                            ->required()
                            ->default('tetap'),

                        Forms\Components\Select::make('functional_position')
                            ->label('Jabatan Fungsional')
                            ->options([
                                'asisten_ahli' => 'Asisten Ahli',
                                'lektor' => 'Lektor',
                                'lektor_kepala' => 'Lektor Kepala',
                                'profesor' => 'Profesor',
                                'tenaga_pengajar' => 'Tenaga Pengajar',
                            ])
                            ->default('tenaga_pengajar'),

                        Forms\Components\TextInput::make('field_of_study')
                            ->label('Bidang Keahlian')
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Password')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ->visible(fn (string $operation): bool => $operation === 'create'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->required()
                            ->same('password')
                            ->visible(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('functional_position')
                    ->label('Jabatan Fungsional')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'profesor' => 'success',
                        'lektor_kepala' => 'warning',
                        'lektor' => 'info',
                        'asisten_ahli' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'asisten_ahli' => 'Asisten Ahli',
                        'lektor' => 'Lektor',
                        'lektor_kepala' => 'Lektor Kepala',
                        'profesor' => 'Profesor',
                        'tenaga_pengajar' => 'Tenaga Pengajar',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('employment_status')
                    ->label('Status Kepegawaian')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tetap' => 'Dosen Tetap',
                        'kontrak' => 'Dosen Kontrak',
                        'honorer' => 'Dosen Honorer',
                        'luar_biasa' => 'Dosen Luar Biasa',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('functional_position')
                    ->label('Jabatan Fungsional')
                    ->options([
                        'asisten_ahli' => 'Asisten Ahli',
                        'lektor' => 'Lektor',
                        'lektor_kepala' => 'Lektor Kepala',
                        'profesor' => 'Profesor',
                        'tenaga_pengajar' => 'Tenaga Pengajar',
                    ]),

                Tables\Filters\SelectFilter::make('employment_status')
                    ->label('Status Kepegawaian')
                    ->options([
                        'tetap' => 'Dosen Tetap',
                        'kontrak' => 'Dosen Kontrak',
                        'honorer' => 'Dosen Honorer',
                        'luar_biasa' => 'Dosen Luar Biasa',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
                    ->label('Import Data Dosen')
                    ->importer(UserImporter::class)
                    ->color('success')
                    ->icon('heroicon-o-arrow-up-tray'),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Dosen'),
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
            'index' => Pages\ListDosens::route('/'),
            'create' => Pages\CreateDosen::route('/create'),
            'edit' => Pages\EditDosen::route('/{record}/edit'),
        ];
    }
}
