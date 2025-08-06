<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'User Management';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'Users';
    protected static ?string $navigationGroup = 'SISTEM';
    protected static ?int $navigationSort = 1;

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
        // Admin tidak bisa delete dirinya sendiri
        return auth()->user()->can('manage_users') && $record->id !== auth()->id();
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

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Data Akademik')
                    ->schema([
                        Forms\Components\TextInput::make('nidn')
                            ->label('NIDN')
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\Select::make('user_type')
                            ->label('Jenis User')
                            ->options([
                                'dosen' => 'Dosen',
                                'admin' => 'Admin',
                                'reviewer' => 'Reviewer',
                                'pimpinan' => 'Pimpinan',
                            ])
                            ->required()
                            ->default('dosen'),

                        Forms\Components\Select::make('employment_status')
                            ->label('Status Kepegawaian')
                            ->options([
                                'tetap' => 'Dosen Tetap',
                                'kontrak' => 'Dosen Kontrak',
                                'tidak_tetap' => 'Dosen Tidak Tetap',
                            ])
                            ->required()
                            ->default('tetap'),

                        Forms\Components\Select::make('functional_position')
                            ->label('Jabatan Fungsional')
                            ->options([
                                'asisten_ahli' => 'Asisten Ahli',
                                'lektor' => 'Lektor',
                                'lektor_kepala' => 'Lektor Kepala',
                                'guru_besar' => 'Guru Besar',
                                'tidak_ada' => 'Belum Ada',
                            ]),

                        Forms\Components\TextInput::make('field_of_study')
                            ->label('Bidang Keahlian')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('System & Roles')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Lainnya')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
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

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user_type')
                    ->label('Jenis User')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'dosen' => 'success',
                        'reviewer' => 'warning',
                        'pimpinan' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->separator(','),

                Tables\Columns\TextColumn::make('employment_status')
                    ->label('Status Kepegawaian')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tetap' => 'success',
                        'kontrak' => 'warning',
                        'tidak_tetap' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('functional_position')
                    ->label('Jabatan Fungsional')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'asisten_ahli' => 'Asisten Ahli',
                        'lektor' => 'Lektor',
                        'lektor_kepala' => 'Lektor Kepala',
                        'guru_besar' => 'Guru Besar',
                        'tidak_ada' => 'Belum Ada',
                        default => $state ?? '-',
                    }),

                Tables\Columns\TextColumn::make('field_of_study')
                    ->label('Bidang Keahlian')
                    ->searchable()
                    ->limit(30),

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
                Tables\Filters\SelectFilter::make('user_type')
                    ->label('Jenis User')
                    ->options([
                        'dosen' => 'Dosen',
                        'admin' => 'Admin',
                        'reviewer' => 'Reviewer',
                        'pimpinan' => 'Pimpinan',
                    ]),

                Tables\Filters\SelectFilter::make('employment_status')
                    ->label('Status Kepegawaian')
                    ->options([
                        'tetap' => 'Dosen Tetap',
                        'kontrak' => 'Dosen Kontrak',
                        'tidak_tetap' => 'Dosen Tidak Tetap',
                    ]),

                Tables\Filters\SelectFilter::make('functional_position')
                    ->label('Jabatan Fungsional')
                    ->options([
                        'asisten_ahli' => 'Asisten Ahli',
                        'lektor' => 'Lektor',
                        'lektor_kepala' => 'Lektor Kepala',
                        'guru_besar' => 'Guru Besar',
                        'tidak_ada' => 'Belum Ada',
                    ]),

                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
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
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah User'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
