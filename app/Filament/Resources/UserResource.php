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
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
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
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable(),
                    ])->columns(2),

                Forms\Components\Section::make('Profil Pengguna')
                    ->schema([
                        Forms\Components\Select::make('user_type')
                            ->label('Tipe Pengguna')
                            ->options([
                                'dosen' => 'Dosen Peneliti',
                                'admin' => 'Admin LPPM',
                                'reviewer' => 'Reviewer Eksternal',
                                'pimpinan' => 'Pimpinan Fakultas',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('nidn')
                            ->label('NIDN')
                            ->unique(ignoreRecord: true)
                            ->visible(fn (callable $get) => $get('user_type') === 'dosen'),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel(),

                        Forms\Components\Select::make('functional_position')
                            ->label('Jabatan Fungsional')
                            ->options([
                                'asisten_ahli' => 'Asisten Ahli',
                                'lektor' => 'Lektor',
                                'lektor_kepala' => 'Lektor Kepala',
                                'guru_besar' => 'Guru Besar',
                            ])
                            ->visible(fn (callable $get) => $get('user_type') === 'dosen'),

                        Forms\Components\TextInput::make('field_of_study')
                            ->label('Bidang Keilmuan')
                            ->visible(fn (callable $get) => $get('user_type') === 'dosen'),

                        Forms\Components\Select::make('employment_status')
                            ->label('Status Kepegawaian')
                            ->options([
                                'tetap' => 'Tetap',
                                'tidak_tetap' => 'Tidak Tetap',
                            ])
                            ->default('tetap')
                            ->visible(fn (callable $get) => $get('user_type') === 'dosen'),
                    ])->columns(2),

                Forms\Components\Section::make('Alamat')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'dosen' => 'success',
                        'reviewer' => 'warning',
                        'pimpinan' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin LPPM',
                        'dosen' => 'Dosen',
                        'reviewer' => 'Reviewer',
                        'pimpinan' => 'Pimpinan',
                    }),

                Tables\Columns\TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('functional_position')
                    ->label('Jabatan')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'asisten_ahli' => 'Asisten Ahli',
                        'lektor' => 'Lektor',
                        'lektor_kepala' => 'Lektor Kepala',
                        'guru_besar' => 'Guru Besar',
                        default => '-',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->label('Tipe Pengguna')
                    ->options([
                        'dosen' => 'Dosen Peneliti',
                        'admin' => 'Admin LPPM',
                        'reviewer' => 'Reviewer Eksternal',
                        'pimpinan' => 'Pimpinan Fakultas',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
