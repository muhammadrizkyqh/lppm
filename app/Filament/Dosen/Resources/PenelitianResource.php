<?php

namespace App\Filament\Dosen\Resources;

use App\Filament\Dosen\Resources\PenelitianResource\Pages;
use App\Models\Penelitian;
use App\Models\KategoriPenelitian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class PenelitianResource extends Resource
{
    protected static ?string $model = Penelitian::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Penelitian Saya';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Penelitian')
                    ->schema([
                        Forms\Components\Select::make('kategori_penelitian_id')
                            ->label('Kategori Penelitian')
                            ->relationship('kategoriPenelitian', 'nama_kategori')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

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

                Forms\Components\Section::make('Dokumen Proposal')
                    ->schema([
                        Forms\Components\FileUpload::make('file_proposal')
                            ->label('File Proposal (PDF)')
                            ->directory('penelitian/proposals')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) // 10MB
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Upload file proposal dalam format PDF. Maksimal 10MB.'),
                    ]),

                // Status dan catatan (read-only untuk dosen)
                Forms\Components\Section::make('Status Pengajuan')
                    ->schema([
                        Forms\Components\Placeholder::make('status_display')
                            ->label('Status')
                            ->content(fn (?Penelitian $record): string => $record?->status_label ?? 'Belum Diajukan'),

                        Forms\Components\Placeholder::make('catatan_verifikasi')
                            ->label('Catatan dari Admin')
                            ->content(fn (?Penelitian $record): string => $record?->catatan_verifikasi ?? 'Belum ada catatan')
                            ->visible(fn (?Penelitian $record): bool => !empty($record?->catatan_verifikasi)),
                    ])
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Filament::auth()->id()))
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Penelitian')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('kategoriPenelitian.nama_kategori')
                    ->label('Kategori')
                    ->badge(),

                Tables\Columns\TextColumn::make('tahun_usulan')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dana_diajukan')
                    ->label('Dana')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    }),

                Tables\Columns\TextColumn::make('luaran_penelitians_count')
                    ->label('Luaran')
                    ->counts('luaranPenelitians')
                    ->badge()
                    ->color('primary'),

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

                Tables\Filters\SelectFilter::make('tahun_usulan')
                    ->options(
                        collect(range(2020, 2030))
                            ->mapWithKeys(fn ($year) => [$year => $year])
                            ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Penelitian $record): bool => $record->status === 'menunggu'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => false), // Disable bulk delete
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
