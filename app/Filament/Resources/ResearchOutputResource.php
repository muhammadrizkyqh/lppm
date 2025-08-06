<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResearchOutputResource\Pages;
use App\Filament\Resources\ResearchOutputResource\RelationManagers;
use App\Models\ResearchOutput;
use App\Models\Proposal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResearchOutputResource extends Resource
{
    protected static ?string $model = ResearchOutput::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Luaran Penelitian';
    protected static ?string $modelLabel = 'Luaran Penelitian';
    protected static ?string $pluralModelLabel = 'Luaran Penelitian';
    protected static ?string $navigationGroup = 'PELAKSANAAN SAYA';
    protected static ?int $navigationSort = 4;

    // Permission checking for research outputs
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('upload_research_output') || auth()->user()->can('verify_research_output');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('upload_research_output');
    }

    public static function canEdit($record): bool
    {
        // Admin can edit all, dosen can edit own outputs
        if (auth()->user()->hasRole('admin')) {
            return auth()->user()->can('verify_research_output');
        }

        return auth()->user()->can('upload_research_output') && $record->proposal->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        // Admin can delete all, dosen can delete own outputs if not verified
        if (auth()->user()->hasRole('admin')) {
            return auth()->user()->can('verify_research_output');
        }

        return auth()->user()->can('upload_research_output') &&
               $record->proposal->user_id === auth()->id() &&
               $record->verification_status !== 'approved';
    }

    public static function canView($record): bool
    {
        // Admin can view all, dosen can view own outputs
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('pimpinan')) {
            return true;
        }

        return $record->proposal->user_id === auth()->id();
    }

    // Query scoping berdasarkan role
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('pimpinan')) {
            return $query;
        }

        // Dosen hanya melihat luaran proposal sendiri
        return $query->whereHas('proposal', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Select::make('proposal_id')
                            ->label('Proposal')
                            ->options(Proposal::where('status', 'approved')->pluck('title', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Luaran')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('Jenis Luaran')
                            ->options([
                                'jurnal_sinta_1' => 'Jurnal Sinta 1',
                                'jurnal_sinta_2' => 'Jurnal Sinta 2',
                                'jurnal_sinta_3' => 'Jurnal Sinta 3',
                                'jurnal_sinta_4' => 'Jurnal Sinta 4',
                                'jurnal_sinta_5' => 'Jurnal Sinta 5',
                                'jurnal_sinta_6' => 'Jurnal Sinta 6',
                                'jurnal_scopus' => 'Jurnal Scopus',
                                'buku_isbn' => 'Buku ISBN',
                                'hki' => 'HKI',
                                'produk_inovasi' => 'Produk Inovasi',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Publikasi')
                    ->schema([
                        Forms\Components\TextInput::make('journal_name')
                            ->label('Nama Jurnal')
                            ->visible(fn (callable $get) => str_starts_with($get('type'), 'jurnal')),

                        Forms\Components\TextInput::make('issn')
                            ->label('ISSN')
                            ->visible(fn (callable $get) => str_starts_with($get('type'), 'jurnal')),

                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->visible(fn (callable $get) => $get('type') === 'buku_isbn'),

                        Forms\Components\TextInput::make('hki_number')
                            ->label('Nomor HKI')
                            ->visible(fn (callable $get) => $get('type') === 'hki'),

                        Forms\Components\DatePicker::make('publication_date')
                            ->label('Tanggal Publikasi'),

                        Forms\Components\TextInput::make('publication_url')
                            ->label('URL Publikasi')
                            ->url()
                            ->placeholder('https://'),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Verifikasi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Disubmit',
                                'published' => 'Dipublikasi',
                                'verified' => 'Terverifikasi',
                            ])
                            ->default('draft'),

                        Forms\Components\Toggle::make('verified_by_admin')
                            ->label('Diverifikasi Admin')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('verified_by')
                            ->label('Diverifikasi Oleh')
                            ->relationship('verifiedBy', 'name')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                Forms\Components\Section::make('Upload Bukti')
                    ->schema([
                        Forms\Components\FileUpload::make('evidence_files')
                            ->label('File Bukti Luaran')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'video/mp4'])
                            ->maxSize(20480) // 20MB
                            ->downloadable()
                            ->previewable()
                            ->helperText('Upload bukti luaran (artikel, scan ISBN, sertifikat HKI, foto/video produk)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proposal.title')
                    ->label('Proposal')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Luaran')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'sinta_1') || str_contains($state, 'scopus') => 'success',
                        str_contains($state, 'sinta_2') || str_contains($state, 'sinta_3') => 'info',
                        str_contains($state, 'sinta_4') || str_contains($state, 'sinta_5') => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'jurnal_sinta_1' => 'Sinta 1',
                        'jurnal_sinta_2' => 'Sinta 2',
                        'jurnal_sinta_3' => 'Sinta 3',
                        'jurnal_sinta_4' => 'Sinta 4',
                        'jurnal_sinta_5' => 'Sinta 5',
                        'jurnal_sinta_6' => 'Sinta 6',
                        'jurnal_scopus' => 'Scopus',
                        'buku_isbn' => 'Buku ISBN',
                        'hki' => 'HKI',
                        'produk_inovasi' => 'Produk',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('journal_name')
                    ->label('Jurnal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'published' => 'info',
                        'verified' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Disubmit',
                        'published' => 'Dipublikasi',
                        'verified' => 'Terverifikasi',
                    }),

                Tables\Columns\IconColumn::make('verified_by_admin')
                    ->label('Verifikasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('publication_date')
                    ->label('Tgl Publikasi')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Luaran')
                    ->options([
                        'jurnal_sinta_1' => 'Jurnal Sinta 1',
                        'jurnal_sinta_2' => 'Jurnal Sinta 2',
                        'jurnal_sinta_3' => 'Jurnal Sinta 3',
                        'jurnal_sinta_4' => 'Jurnal Sinta 4',
                        'jurnal_sinta_5' => 'Jurnal Sinta 5',
                        'jurnal_sinta_6' => 'Jurnal Sinta 6',
                        'jurnal_scopus' => 'Jurnal Scopus',
                        'buku_isbn' => 'Buku ISBN',
                        'hki' => 'HKI',
                        'produk_inovasi' => 'Produk Inovasi',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Disubmit',
                        'published' => 'Dipublikasi',
                        'verified' => 'Terverifikasi',
                    ]),

                Tables\Filters\TernaryFilter::make('verified_by_admin')
                    ->label('Status Verifikasi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (ResearchOutput $record) {
                        $record->update([
                            'verified_by_admin' => true,
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                            'status' => 'verified',
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (ResearchOutput $record) => !$record->verified_by_admin),

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
            'index' => Pages\ListResearchOutputs::route('/'),
            'create' => Pages\CreateResearchOutput::route('/create'),
            'edit' => Pages\EditResearchOutput::route('/{record}/edit'),
        ];
    }
}
