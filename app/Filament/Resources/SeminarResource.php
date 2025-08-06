<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeminarResource\Pages;
use App\Filament\Resources\SeminarResource\RelationManagers;
use App\Models\Seminar;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Infolist;

class SeminarResource extends Resource
{
    protected static ?string $model = Seminar::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationLabel = 'Seminar';

    protected static ?string $modelLabel = 'Seminar';

    protected static ?string $pluralModelLabel = 'Seminar';

    protected static ?string $navigationGroup = 'PELAKSANAAN SAYA';

    protected static ?int $navigationSort = 2;

    // Permission checking for seminars
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('schedule_seminar') || auth()->user()->can('upload_presentation_material');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('schedule_seminar');
    }

    public static function canEdit($record): bool
    {
        if (auth()->user()->hasRole('admin')) {
            return auth()->user()->can('schedule_seminar');
        }

        return $record->proposal->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('schedule_seminar');
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
                Section::make('Informasi Seminar')
                    ->description('Data utama seminar penelitian')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('proposal_id')
                                    ->label('Proposal Penelitian')
                                    ->options(Proposal::with('user')->get()->pluck('title_with_researcher', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $proposal = Proposal::find($state);
                                            if ($proposal) {
                                                $set('title', 'Seminar Hasil Penelitian: ' . $proposal->title);
                                            }
                                        }
                                    }),

                                Forms\Components\Select::make('scheduled_by')
                                    ->label('Dijadwalkan Oleh')
                                    ->options(User::role(['admin', 'pimpinan'])->pluck('name', 'id'))
                                    ->default(auth()->id())
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Seminar')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Jadwal dan Lokasi')
                    ->description('Pengaturan waktu dan tempat seminar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Jenis Seminar')
                                    ->options([
                                        'internal' => 'Internal',
                                        'public' => 'Publik',
                                    ])
                                    ->required()
                                    ->default('internal'),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'scheduled' => 'Terjadwal',
                                        'ongoing' => 'Berlangsung',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->required()
                                    ->default('scheduled'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('scheduled_at')
                                    ->label('Waktu Pelaksanaan')
                                    ->required()
                                    ->minDate(now())
                                    ->displayFormat('d/m/Y H:i'),

                                Forms\Components\TextInput::make('location')
                                    ->label('Lokasi')
                                    ->required()
                                    ->placeholder('Ruang Meeting LPPM / Online'),
                            ]),

                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Link Meeting (Opsional)')
                            ->url()
                            ->placeholder('https://zoom.us/j/...')
                            ->columnSpanFull(),
                    ]),

                Section::make('Agenda dan Hasil')
                    ->description('Agenda seminar dan hasil yang dicapai')
                    ->schema([
                        Forms\Components\RichEditor::make('agenda')
                            ->label('Agenda Seminar')
                            ->placeholder('1. Pembukaan\n2. Presentasi Hasil Penelitian\n3. Diskusi dan Tanya Jawab\n4. Penutup')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('notes')
                            ->label('Catatan Hasil Seminar')
                            ->placeholder('Ringkasan hasil seminar, poin-poin penting yang dibahas')
                            ->columnSpanFull()
                            ->hiddenOn('create'),

                        Forms\Components\RichEditor::make('feedback')
                            ->label('Masukan dan Saran')
                            ->placeholder('Masukan dari peserta, saran perbaikan, dll')
                            ->columnSpanFull()
                            ->hiddenOn('create'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proposal.title')
                    ->label('Proposal')
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->proposal->title;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Seminar')
                    ->limit(40)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis')
                    ->colors([
                        'primary' => 'internal',
                        'success' => 'public',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'internal' => 'Internal',
                        'public' => 'Publik',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'scheduled',
                        'primary' => 'ongoing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Terjadwal',
                        'ongoing' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('scheduledBy.name')
                    ->label('Dijadwalkan Oleh')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis Seminar')
                    ->options([
                        'internal' => 'Internal',
                        'public' => 'Publik',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'scheduled' => 'Terjadwal',
                        'ongoing' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Seminar Mendatang')
                    ->query(fn (Builder $query): Builder => $query->upcoming()),

                Tables\Filters\Filter::make('this_month')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query): Builder => $query->thisMonth()),
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
            ])
            ->defaultSort('scheduled_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Informasi Seminar')
                    ->schema([
                        TextEntry::make('proposal.title')
                            ->label('Proposal Penelitian'),
                        TextEntry::make('title')
                            ->label('Judul Seminar'),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])->columns(2),

                InfoSection::make('Jadwal dan Lokasi')
                    ->schema([
                        TextEntry::make('type')
                            ->label('Jenis Seminar')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'internal' => 'primary',
                                'public' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'internal' => 'Internal',
                                'public' => 'Publik',
                                default => $state,
                            }),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'scheduled' => 'warning',
                                'ongoing' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'scheduled' => 'Terjadwal',
                                'ongoing' => 'Berlangsung',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                                default => $state,
                            }),
                        TextEntry::make('scheduled_at')
                            ->label('Waktu Pelaksanaan')
                            ->dateTime('d F Y, H:i'),
                        TextEntry::make('location')
                            ->label('Lokasi'),
                        TextEntry::make('meeting_link')
                            ->label('Link Meeting')
                            ->url(fn($record) => $record->meeting_link)
                            ->openUrlInNewTab(),
                        TextEntry::make('scheduledBy.name')
                            ->label('Dijadwalkan Oleh'),
                    ])->columns(2),

                InfoSection::make('Agenda dan Hasil')
                    ->schema([
                        TextEntry::make('agenda')
                            ->label('Agenda Seminar')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('Catatan Hasil Seminar')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('feedback')
                            ->label('Masukan dan Saran')
                            ->html()
                            ->columnSpanFull(),
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
            'index' => Pages\ListSeminars::route('/'),
            'create' => Pages\CreateSeminar::route('/create'),
            'edit' => Pages\EditSeminar::route('/{record}/edit'),
        ];
    }
}
