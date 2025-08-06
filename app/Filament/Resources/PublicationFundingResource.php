<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicationFundingResource\Pages;
use App\Filament\Resources\PublicationFundingResource\RelationManagers;
use App\Models\PublicationFunding;
use App\Models\Proposal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;

class PublicationFundingResource extends Resource
{
    protected static ?string $model = PublicationFunding::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Pendanaan Publikasi';

    protected static ?string $modelLabel = 'Pendanaan Publikasi';

    protected static ?string $pluralModelLabel = 'Pendanaan Publikasi';

    protected static ?string $navigationGroup = 'PELAKSANAAN PENELITIAN';

    protected static ?int $navigationSort = 3;

    // Permission checking for publication funding
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('upload_research_output') || auth()->user()->can('approve_publication_funding');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('upload_research_output');
    }

    public static function canEdit($record): bool
    {
        if (auth()->user()->hasRole('admin')) {
            return auth()->user()->can('approve_publication_funding');
        }
        
        return $record->proposal->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('approve_publication_funding');
    }

    public static function canView($record): bool
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('pimpinan')) {
            return true;
        }
        
        return $record->proposal->user_id === auth()->id();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('pimpinan')) {
            return $query;
        }
        
        return $query->whereHas('proposal', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Proposal')
                    ->description('Pilih proposal yang akan dipublikasikan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('proposal_id')
                                    ->label('Proposal Penelitian')
                                    ->options(Proposal::approved()->with('user')->get()->pluck('title_with_researcher', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $proposal = Proposal::find($state);
                                            if ($proposal) {
                                                $set('user_id', $proposal->user_id);
                                            }
                                        }
                                    }),

                                Forms\Components\Select::make('user_id')
                                    ->label('Pengaju')
                                    ->options(User::role('dosen')->pluck('name', 'id'))
                                    ->default(auth()->id())
                                    ->required()
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Detail Publikasi')
                    ->description('Informasi tentang publikasi yang akan diajukan')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Publikasi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('publication_type')
                                    ->label('Jenis Publikasi')
                                    ->options([
                                        'journal_national' => 'Jurnal Nasional',
                                        'journal_international' => 'Jurnal Internasional',
                                        'conference_national' => 'Konferensi Nasional',
                                        'conference_international' => 'Konferensi Internasional',
                                        'book' => 'Buku',
                                        'book_chapter' => 'Bab Buku',
                                    ])
                                    ->required()
                                    ->reactive(),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'submitted' => 'Diajukan',
                                        'under_review' => 'Sedang Direview',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'published' => 'Diterbitkan',
                                    ])
                                    ->required()
                                    ->default('draft')
                                    ->visibleOn(['edit']),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('journal_name')
                                    ->label('Nama Jurnal/Konferensi')
                                    ->placeholder('Nama jurnal atau konferensi target')
                                    ->visible(fn (callable $get) => in_array($get('publication_type'), ['journal_national', 'journal_international', 'conference_national', 'conference_international'])),

                                Forms\Components\TextInput::make('publisher')
                                    ->label('Penerbit')
                                    ->placeholder('Nama penerbit')
                                    ->visible(fn (callable $get) => in_array($get('publication_type'), ['book', 'book_chapter'])),
                            ]),

                        Forms\Components\RichEditor::make('abstract')
                            ->label('Abstrak Publikasi')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Rencana Biaya')
                    ->description('Estimasi biaya publikasi dan timeline')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('estimated_cost')
                                    ->label('Estimasi Biaya')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->placeholder('5000000'),

                                Forms\Components\TextInput::make('approved_amount')
                                    ->label('Jumlah Disetujui')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->hiddenOn('create')
                                    ->placeholder('Akan diisi setelah review'),
                            ]),

                        Forms\Components\RichEditor::make('cost_breakdown')
                            ->label('Rincian Biaya')
                            ->required()
                            ->placeholder('
                            <ul>
                                <li>Biaya publikasi jurnal: Rp 3.000.000</li>
                                <li>Biaya proofreading: Rp 1.000.000</li>
                                <li>Biaya translate: Rp 1.000.000</li>
                            </ul>')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('submission_deadline')
                                    ->label('Target Deadline Submit')
                                    ->displayFormat('d/m/Y'),

                                Forms\Components\DatePicker::make('expected_publication_date')
                                    ->label('Target Tanggal Terbit')
                                    ->displayFormat('d/m/Y'),
                            ]),
                    ]),

                Section::make('Review dan Hasil')
                    ->description('Catatan review dan hasil publikasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('reviewed_by')
                                    ->label('Direview Oleh')
                                    ->options(User::role(['admin', 'pimpinan'])->pluck('name', 'id'))
                                    ->hiddenOn('create'),

                                Forms\Components\DateTimePicker::make('reviewed_at')
                                    ->label('Tanggal Review')
                                    ->hiddenOn('create'),
                            ]),

                        Forms\Components\RichEditor::make('reviewer_notes')
                            ->label('Catatan Reviewer')
                            ->placeholder('Catatan dan masukan dari reviewer')
                            ->columnSpanFull()
                            ->hiddenOn('create'),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('publication_url')
                                    ->label('URL Publikasi')
                                    ->url()
                                    ->placeholder('https://journal.example.com/article/123')
                                    ->hiddenOn('create'),

                                Forms\Components\TextInput::make('doi')
                                    ->label('DOI')
                                    ->placeholder('10.1234/example.2025.123')
                                    ->hiddenOn('create'),
                            ]),
                    ])
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proposal.title')
                    ->label('Proposal')
                    ->limit(25)
                    ->tooltip(function ($record) {
                        return $record->proposal->title;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Publikasi')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengaju')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('publication_type')
                    ->label('Jenis')
                    ->colors([
                        'primary' => 'journal_national',
                        'success' => 'journal_international',
                        'warning' => 'conference_national',
                        'info' => 'conference_international',
                        'secondary' => 'book',
                        'gray' => 'book_chapter',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'journal_national' => 'Jurnal Nasional',
                        'journal_international' => 'Jurnal Internasional',
                        'conference_national' => 'Konferensi Nasional',
                        'conference_international' => 'Konferensi Internasional',
                        'book' => 'Buku',
                        'book_chapter' => 'Bab Buku',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('estimated_cost')
                    ->label('Estimasi Biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('approved_amount')
                    ->label('Disetujui')
                    ->money('IDR')
                    ->placeholder('Belum direview')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'submitted',
                        'primary' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'info' => 'published',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'under_review' => 'Sedang Direview',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'published' => 'Diterbitkan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('submission_deadline')
                    ->label('Deadline Submit')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('publication_type')
                    ->label('Jenis Publikasi')
                    ->options([
                        'journal_national' => 'Jurnal Nasional',
                        'journal_international' => 'Jurnal Internasional',
                        'conference_national' => 'Konferensi Nasional',
                        'conference_international' => 'Konferensi Internasional',
                        'book' => 'Buku',
                        'book_chapter' => 'Bab Buku',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'under_review' => 'Sedang Direview',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'published' => 'Diterbitkan',
                    ]),

                Tables\Filters\Filter::make('pending_review')
                    ->label('Perlu Review')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'under_review')),

                Tables\Filters\Filter::make('approved')
                    ->label('Disetujui')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'approved')),

                Tables\Filters\Filter::make('published')
                    ->label('Sudah Terbit')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'published')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pendanaan Publikasi')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pendanaan publikasi ini?')
                    ->form([
                        Forms\Components\TextInput::make('approved_amount')
                            ->label('Jumlah Dana Disetujui')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\Textarea::make('reviewer_notes')
                            ->label('Catatan Review')
                            ->placeholder('Catatan atau masukan untuk pengaju'),
                    ])
                    ->action(function (array $data, PublicationFunding $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_amount' => $data['approved_amount'],
                            'reviewer_notes' => $data['reviewer_notes'] ?? null,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                    })
                    ->visible(fn (PublicationFunding $record): bool => $record->status === 'under_review'),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pendanaan Publikasi')
                    ->modalDescription('Apakah Anda yakin ingin menolak pendanaan publikasi ini?')
                    ->form([
                        Forms\Components\Textarea::make('reviewer_notes')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Jelaskan alasan penolakan'),
                    ])
                    ->action(function (array $data, PublicationFunding $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'reviewer_notes' => $data['reviewer_notes'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                    })
                    ->visible(fn (PublicationFunding $record): bool => $record->status === 'under_review'),

                Tables\Actions\Action::make('mark_published')
                    ->label('Tandai Terbit')
                    ->icon('heroicon-o-document-check')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('publication_url')
                            ->label('URL Publikasi')
                            ->url()
                            ->required(),
                        Forms\Components\TextInput::make('doi')
                            ->label('DOI')
                            ->placeholder('10.1234/example.2025.123'),
                    ])
                    ->action(function (array $data, PublicationFunding $record): void {
                        $record->update([
                            'status' => 'published',
                            'publication_url' => $data['publication_url'],
                            'doi' => $data['doi'] ?? null,
                        ]);
                    })
                    ->visible(fn (PublicationFunding $record): bool => $record->status === 'approved'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPublicationFundings::route('/'),
            'create' => Pages\CreatePublicationFunding::route('/create'),
            'edit' => Pages\EditPublicationFunding::route('/{record}/edit'),
        ];
    }
}
