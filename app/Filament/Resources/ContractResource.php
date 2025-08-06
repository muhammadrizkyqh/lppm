<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Models\Contract;
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
use Carbon\Carbon;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Kontrak';

    protected static ?string $modelLabel = 'Kontrak';

    protected static ?string $pluralModelLabel = 'Kontrak';

    protected static ?string $navigationGroup = 'PELAKSANAAN PENELITIAN';

    protected static ?int $navigationSort = 1;

    // Permission checking for contract management
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('confirm_fund_stage_1') || auth()->user()->can('view_proposal');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('confirm_fund_stage_1'); // Admin can create contracts
    }

    public static function canEdit($record): bool
    {
        // Admin can edit all, dosen can edit if it's their proposal
        if (auth()->user()->hasRole('admin')) {
            return auth()->user()->can('confirm_fund_stage_1');
        }
        
        return $record->proposal->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('confirm_fund_stage_1'); // Only admin can delete
    }

    public static function canView($record): bool
    {
        // Admin can view all, dosen can view their own contracts
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
        
        // Dosen hanya melihat kontrak proposal sendiri
        return $query->whereHas('proposal', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar Kontrak')
                    ->description('Data utama kontrak penelitian')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('contract_number')
                                    ->label('Nomor Kontrak')
                                    ->disabled()
                                    ->default(function () {
                                        $year = now()->year;
                                        $month = now()->format('m');
                                        $count = Contract::whereYear('created_at', $year)->count() + 1;
                                        return sprintf('LPPM/%s/%s/%04d', $year, $month, $count);
                                    })
                                    ->unique(ignoreRecord: true),

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
                                                $set('title', 'Kontrak Penelitian: ' . $proposal->title);
                                                $set('second_party_user_id', $proposal->user_id);
                                                $set('contract_value', $proposal->fund_amount);
                                            }
                                        }
                                    }),
                            ]),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Kontrak')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Jenis Kontrak')
                                    ->options([
                                        'research' => 'Penelitian',
                                        'service' => 'Pengabdian',
                                        'collaboration' => 'Kerjasama',
                                    ])
                                    ->required()
                                    ->default('research'),

                                Forms\Components\Select::make('status')
                                    ->label('Status Kontrak')
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending_signature' => 'Menunggu Tanda Tangan',
                                        'signed' => 'Ditandatangani',
                                        'active' => 'Aktif',
                                        'completed' => 'Selesai',
                                        'terminated' => 'Dibatalkan',
                                    ])
                                    ->required()
                                    ->default('draft'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Kontrak')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Nilai dan Durasi Kontrak')
                    ->description('Informasi keuangan dan timeline kontrak')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('contract_value')
                                    ->label('Nilai Kontrak')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->placeholder('50000000'),

                                Forms\Components\TextInput::make('duration_months')
                                    ->label('Durasi (Bulan)')
                                    ->numeric()
                                    ->required()
                                    ->default(12)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state && $get('start_date')) {
                                            $startDate = Carbon::parse($get('start_date'));
                                            $endDate = $startDate->copy()->addMonths($state);
                                            $set('end_date', $endDate->format('Y-m-d'));
                                        }
                                    }),

                                Forms\Components\Select::make('created_by')
                                    ->label('Dibuat Oleh')
                                    ->options(User::role(['admin', 'pimpinan'])->pluck('name', 'id'))
                                    ->default(auth()->id())
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state && $get('duration_months')) {
                                            $startDate = Carbon::parse($state);
                                            $endDate = $startDate->copy()->addMonths($get('duration_months'));
                                            $set('end_date', $endDate->format('Y-m-d'));
                                        }
                                    }),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Tanggal Berakhir')
                                    ->required()
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Pihak yang Terlibat')
                    ->description('Informasi pihak pertama dan kedua dalam kontrak')
                    ->schema([
                        Forms\Components\TextInput::make('first_party')
                            ->label('Pihak Pertama (Institusi)')
                            ->required()
                            ->default('STAI Ali bin Abi Thalib Surabaya')
                            ->maxLength(255),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_party_representative')
                                    ->label('Wakil Pihak Pertama')
                                    ->required()
                                    ->placeholder('Dr. H. Ahmad Rektor')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('first_party_position')
                                    ->label('Jabatan Wakil Pihak Pertama')
                                    ->required()
                                    ->default('Rektor')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('second_party_user_id')
                                    ->label('Pihak Kedua (Peneliti)')
                                    ->options(User::role('dosen')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Forms\Components\TextInput::make('second_party_position')
                                    ->label('Jabatan Peneliti')
                                    ->required()
                                    ->placeholder('Dosen Tetap')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Deliverables dan Pembayaran')
                    ->description('Rincian deliverables dan jadwal pembayaran')
                    ->schema([
                        Forms\Components\Repeater::make('deliverables')
                            ->label('Deliverables')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Deliverable')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Tenggat Waktu')
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'in_progress' => 'Sedang Dikerjakan',
                                        'completed' => 'Selesai',
                                    ])
                                    ->default('pending'),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('payment_schedule')
                            ->label('Jadwal Pembayaran')
                            ->schema([
                                Forms\Components\TextInput::make('stage')
                                    ->label('Tahap')
                                    ->required(),
                                Forms\Components\TextInput::make('percentage')
                                    ->label('Persentase (%)')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Dibayar',
                                        'overdue' => 'Terlambat',
                                    ])
                                    ->default('pending'),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),

                Section::make('Syarat dan Ketentuan')
                    ->description('Ketentuan umum dan khusus kontrak')
                    ->schema([
                        Forms\Components\RichEditor::make('terms_and_conditions')
                            ->label('Syarat dan Ketentuan Umum')
                            ->required()
                            ->default('<ol><li>Pihak kedua wajib melaksanakan penelitian sesuai dengan proposal yang telah disetujui</li><li>Hasil penelitian menjadi hak milik STAI Ali bin Abi Thalib Surabaya</li><li>Pihak kedua wajib menyerahkan laporan kemajuan setiap 3 bulan</li><li>Pembayaran akan dilakukan sesuai jadwal yang telah ditentukan</li><li>Kontrak dapat dibatalkan jika pihak kedua tidak memenuhi kewajiban</li></ol>')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('special_conditions')
                            ->label('Ketentuan Khusus')
                            ->placeholder('Ketentuan khusus yang berlaku untuk kontrak ini')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status Penandatanganan')
                    ->description('Informasi penandatanganan kontrak')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('signed_by_first_party')
                                    ->label('Ditandatangani Pihak Pertama Oleh')
                                    ->options(User::role(['admin', 'pimpinan'])->pluck('name', 'id'))
                                    ->hiddenOn('create'),

                                Forms\Components\DateTimePicker::make('signed_at')
                                    ->label('Tanggal Tanda Tangan Pihak Pertama')
                                    ->hiddenOn('create'),
                            ]),

                        Forms\Components\DateTimePicker::make('second_party_signed_at')
                            ->label('Tanggal Tanda Tangan Pihak Kedua')
                            ->hiddenOn('create'),
                    ])
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('Nomor Kontrak')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Kontrak')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('secondParty.name')
                    ->label('Peneliti')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis')
                    ->colors([
                        'primary' => 'research',
                        'success' => 'service',
                        'warning' => 'collaboration',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'research' => 'Penelitian',
                        'service' => 'Pengabdian',
                        'collaboration' => 'Kerjasama',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('contract_value')
                    ->label('Nilai Kontrak')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'pending_signature',
                        'primary' => 'signed',
                        'success' => 'active',
                        'info' => 'completed',
                        'danger' => 'terminated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'pending_signature' => 'Menunggu TTD',
                        'signed' => 'Ditandatangani',
                        'active' => 'Aktif',
                        'completed' => 'Selesai',
                        'terminated' => 'Dibatalkan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('duration_months')
                    ->label('Durasi')
                    ->suffix(' bulan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis Kontrak')
                    ->options([
                        'research' => 'Penelitian',
                        'service' => 'Pengabdian',
                        'collaboration' => 'Kerjasama',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_signature' => 'Menunggu Tanda Tangan',
                        'signed' => 'Ditandatangani',
                        'active' => 'Aktif',
                        'completed' => 'Selesai',
                        'terminated' => 'Dibatalkan',
                    ]),

                Tables\Filters\Filter::make('active_contracts')
                    ->label('Kontrak Aktif')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),

                Tables\Filters\Filter::make('pending_signature')
                    ->label('Perlu Tanda Tangan')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending_signature')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('send_for_signature')
                    ->label('Kirim untuk TTD')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Contract $record): void {
                        $record->update(['status' => 'pending_signature']);
                    })
                    ->visible(fn (Contract $record): bool => $record->status === 'draft'),

                Tables\Actions\Action::make('sign_first_party')
                    ->label('TTD Pihak Pertama')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (Contract $record): void {
                        $record->update([
                            'signed_at' => now(),
                            'signed_by_first_party' => auth()->id(),
                            'status' => $record->second_party_signed_at ? 'signed' : 'pending_signature',
                        ]);
                    })
                    ->visible(fn (Contract $record): bool =>
                        $record->status === 'pending_signature' && !$record->signed_at
                    ),

                Tables\Actions\Action::make('sign_second_party')
                    ->label('TTD Pihak Kedua')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Contract $record): void {
                        $record->update([
                            'second_party_signed_at' => now(),
                            'status' => $record->signed_at ? 'signed' : 'pending_signature',
                        ]);
                    })
                    ->visible(fn (Contract $record): bool =>
                        $record->status === 'pending_signature' && !$record->second_party_signed_at
                    ),

                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Contract $record): void {
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn (Contract $record): bool =>
                        $record->status === 'signed' && $record->isFullySigned()
                    ),

                Tables\Actions\Action::make('complete')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (Contract $record): void {
                        $record->update(['status' => 'completed']);
                    })
                    ->visible(fn (Contract $record): bool => $record->status === 'active'),
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
