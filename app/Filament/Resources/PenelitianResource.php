<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenelitianResource\Pages;
use App\Models\Penelitian;
use App\Models\KategoriPenelitian;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;

class PenelitianResource extends Resource
{
    protected static ?string $model = Penelitian::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Penelitian';

    protected static ?string $navigationGroup = 'Manajemen Penelitian';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peneliti')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Dosen')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('kategori_penelitian_id')
                            ->label('Kategori Penelitian')
                            ->relationship('kategoriPenelitian', 'nama_kategori')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Penelitian')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul Penelitian')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('bidang_ilmu')
                            ->label('Bidang Ilmu')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('mitra')
                            ->label('Mitra (Opsional)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tahun_usulan')
                            ->label('Tahun Usulan')
                            ->numeric()
                            ->required()
                            ->minValue(2020)
                            ->maxValue(2030)
                            ->default(date('Y'))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('dana_diajukan')
                            ->label('Dana Diajukan')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Jadwal Penelitian')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->after('tanggal_mulai')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dokumen dan Status')
                    ->schema([
                        Forms\Components\FileUpload::make('file_proposal')
                            ->label('File Proposal')
                            ->directory('penelitian/proposals')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) // 10MB
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'menunggu' => 'Menunggu Verifikasi',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->default('menunggu')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Catatan Verifikasi')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Berikan catatan untuk dosen mengenai status pengajuan.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Penelitian')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('kategoriPenelitian.nama_kategori')
                    ->label('Kategori')
                    ->badge(),

                Tables\Columns\TextColumn::make('tahun_usulan')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dana_diajukan')
                    ->label('Dana')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('kategori_penelitian_id')
                    ->label('Kategori')
                    ->relationship('kategoriPenelitian', 'nama_kategori'),

                Tables\Filters\SelectFilter::make('tahun_usulan')
                    ->options(
                        collect(range(2020, 2030))
                            ->mapWithKeys(fn ($year) => [$year => $year])
                            ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Penelitian $record): bool => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->action(fn (Penelitian $record) => $record->update(['status' => 'disetujui']))
                    ->after(function () {
                        \Filament\Notifications\Notification::make()
                            ->title('Penelitian berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Penelitian $record): bool => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->action(fn (Penelitian $record) => $record->update(['status' => 'ditolak']))
                    ->after(function () {
                        \Filament\Notifications\Notification::make()
                            ->title('Penelitian berhasil ditolak')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
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
            'index' => Pages\ListPenelitians::route('/'),
            'create' => Pages\CreatePenelitian::route('/create'),
            // 'view' => Pages\ViewPenelitian::route('/{record}'),
            'edit' => Pages\EditPenelitian::route('/{record}/edit'),
        ];
    }
}
