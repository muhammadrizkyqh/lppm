<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalResource\Pages;
use App\Filament\Resources\ProposalResource\RelationManagers;
use App\Models\Proposal;
use App\Models\ResearchScheme;
use App\Models\User;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Daftar Proposal';
    protected static ?string $modelLabel = 'Proposal';
    protected static ?string $pluralModelLabel = 'Proposal';
    protected static ?string $navigationGroup = 'PROPOSAL SAYA';
    protected static ?int $navigationSort = 1;

    // Dynamic navigation based on role
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('admin') ? 'ADMINISTRASI' : 'PROPOSAL SAYA';
    }

    public static function getNavigationLabel(): string
    {
        return auth()->user()->hasRole('admin') ? 'Semua Proposal' : 'Daftar Proposal';
    }

    public static function getNavigationSort(): ?int
    {
        return auth()->user()->hasRole('admin') ? 1 : 1;
    }

    // Permission checking methods
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('view_proposal');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_proposal');
    }

    public static function canEdit($record): bool
    {
        // Admin dapat edit semua, dosen hanya proposal sendiri
        if (auth()->user()->hasRole('admin')) {
            return auth()->user()->can('edit_proposal');
        }

        return auth()->user()->can('edit_proposal') && $record->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        // Hanya admin atau pemilik proposal yang bisa delete (dan proposal masih draft)
        $canDelete = auth()->user()->hasRole('admin') || $record->user_id === auth()->id();
        return $canDelete && $record->status === 'draft';
    }

    public static function canView($record): bool
    {
        // Admin dapat lihat semua, reviewer untuk proposal yang ditugaskan, dosen untuk proposal sendiri
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        if (auth()->user()->hasRole('reviewer')) {
            return $record->reviews()->where('reviewer_id', auth()->id())->exists();
        }

        return $record->user_id === auth()->id();
    }

    // Query scoping berdasarkan role
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('pimpinan')) {
            // Admin dan pimpinan dapat melihat semua proposal
            return $query;
        }

        if (auth()->user()->hasRole('reviewer')) {
            // Reviewer hanya melihat proposal yang ditugaskan
            return $query->whereHas('reviews', function ($q) {
                $q->where('reviewer_id', auth()->id());
            });
        }

        // Dosen hanya melihat proposal sendiri
        return $query->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Penelitian/Pengabdian')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('research_scheme_id')
                            ->label('Skema')
                            ->options(ResearchScheme::active()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $scheme = ResearchScheme::find($state);
                                    if ($scheme) {
                                        $set('fund_amount', $scheme->default_fund_amount);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('user_id')
                            ->label('Ketua Tim')
                            ->options(User::where('user_type', 'dosen')
                                ->where('employment_status', 'tetap')
                                ->where('is_active', true)
                                ->whereNotNull('functional_position')
                                ->whereNotIn('functional_position', ['tidak_ada', 'non_fungsional'])
                                ->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required()
                            ->searchable()
                            ->disabled(fn() => !auth()->user()->hasRole('admin'))
                            ->helperText(auth()->user()->hasRole('admin')
                                ? 'Admin dapat memilih ketua tim, dosen otomatis menjadi ketua'
                                : 'Anda akan menjadi ketua tim proposal ini')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state) {
                                    $validationError = Proposal::validateLeaderRules($state);
                                    if ($validationError) {
                                        $set('user_id', null);
                                        \Filament\Notifications\Notification::make()
                                            ->title('Validasi Gagal')
                                            ->body($validationError)
                                            ->danger()
                                            ->send();
                                    }
                                }
                            }),
                    ])->columns(2),

                Forms\Components\Section::make('Abstrak dan Kata Kunci')
                    ->schema([
                        Forms\Components\Textarea::make('abstract')
                            ->label('Abstrak (maksimal 250 kata)')
                            ->required()
                            ->maxLength(2000)
                            ->rows(5),

                        Forms\Components\TextInput::make('keywords')
                            ->label('Kata Kunci (pisahkan dengan koma)')
                            ->required()
                            ->helperText('Masukkan 3-6 kata kunci, pisahkan dengan koma'),
                    ]),

                Forms\Components\Section::make('Anggaran dan Publikasi')
                    ->schema([
                        Forms\Components\TextInput::make('fund_amount')
                            ->label('Anggaran Dana (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\Textarea::make('publication_plan')
                            ->label('Rencana Publikasi')
                            ->helperText('Jelaskan rencana publikasi hasil penelitian/pengabdian')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Tim Anggota')
                    ->schema([
                        Forms\Components\Repeater::make('teamMembers')
                            ->relationship('teamMembers')
                            ->schema([
                                Forms\Components\Select::make('member_type')
                                    ->label('Jenis Anggota')
                                    ->options([
                                        'dosen' => 'Dosen',
                                        'mahasiswa' => 'Mahasiswa',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(1),

                                Forms\Components\Select::make('user_id')
                                    ->label('Pilih Dosen')
                                    ->options(User::where('user_type', 'dosen')
                                        ->where('is_active', true)
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->visible(fn (callable $get) => $get('member_type') === 'dosen')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        if ($state) {
                                            $validationError = Proposal::validateMemberRules($state);
                                            if ($validationError) {
                                                $set('user_id', null);
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Validasi Anggota Gagal')
                                                    ->body($validationError)
                                                    ->danger()
                                                    ->send();
                                            } else {
                                                $user = User::find($state);
                                                if ($user) {
                                                    $set('name', $user->name);
                                                    $set('nidn', $user->nidn);
                                                    $set('email', $user->email);
                                                    $set('phone', $user->phone);
                                                    $set('position', $user->functional_position);
                                                    $set('expertise', $user->field_of_study);
                                                }
                                            }
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\Select::make('student_id')
                                    ->label('Pilih Mahasiswa')
                                    ->options(Student::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->visible(fn (callable $get) => $get('member_type') === 'mahasiswa')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $validationError = Proposal::validateStudentMemberRules($state);
                                            if ($validationError) {
                                                $set('student_id', null);
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Validasi Mahasiswa Gagal')
                                                    ->body($validationError)
                                                    ->danger()
                                                    ->send();
                                            } else {
                                                $student = Student::find($state);
                                                if ($student) {
                                                    $set('name', $student->name);
                                                    $set('nim', $student->nim);
                                                    $set('email', $student->email);
                                                    $set('phone', $student->phone);
                                                    $set('study_program', $student->study_program);
                                                    $set('semester', $student->semester);
                                                }
                                            }
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('nidn')
                                    ->label('NIDN')
                                    ->visible(fn (callable $get) => $get('member_type') === 'dosen')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('nim')
                                    ->label('NIM')
                                    ->visible(fn (callable $get) => $get('member_type') === 'mahasiswa')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('position')
                                    ->label('Jabatan Fungsional')
                                    ->visible(fn (callable $get) => $get('member_type') === 'dosen')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('expertise')
                                    ->label('Bidang Keahlian')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('study_program')
                                    ->label('Program Studi')
                                    ->visible(fn (callable $get) => $get('member_type') === 'mahasiswa')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('semester')
                                    ->label('Semester')
                                    ->numeric()
                                    ->visible(fn (callable $get) => $get('member_type') === 'mahasiswa')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('phone')
                                    ->label('No. HP')
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->addActionLabel('Tambah Anggota Tim')
                            ->maxItems(3)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->helperText('Batasan Tim: Maksimal 4 orang (1 ketua + 3 anggota). Dosen maksimal 4 proposal/tahun, Mahasiswa maksimal 1 proposal/tahun. Tidak boleh ada duplikasi anggota.')
                            ->itemLabel(function (array $state): ?string {
                                $name = $state['name'] ?? 'Anggota Baru';
                                $type = $state['member_type'] ?? '';
                                $identifier = '';

                                if ($type === 'dosen' && isset($state['nidn'])) {
                                    $identifier = " (NIDN: {$state['nidn']})";
                                } elseif ($type === 'mahasiswa' && isset($state['nim'])) {
                                    $identifier = " (NIM: {$state['nim']})";
                                }

                                return $name . $identifier;
                            }),
                    ]),

                Forms\Components\Section::make('Upload Dokumen')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('proposal_documents')
                            ->collection('proposal_documents')
                            ->label('Dokumen Proposal Lengkap')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240) // 10MB
                            ->multiple()
                            ->downloadable()
                            ->previewable()
                            ->helperText('Upload dokumen proposal dalam format PDF atau Word (maksimal 10MB)'),

                        SpatieMediaLibraryFileUpload::make('final_reports')
                            ->collection('final_reports')
                            ->label('Laporan Akhir')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240)
                            ->multiple()
                            ->downloadable()
                            ->previewable()
                            ->helperText('Upload laporan akhir penelitian/pengabdian')
                            ->visible(fn (callable $get) => in_array($get('status'), ['approved'])),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Diajukan',
                                'under_review' => 'Sedang Direview',
                                'revision_required' => 'Perlu Revisi',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('draft')
                            ->visible(fn () => auth()->user()->can('approve_proposal')),
                    ])
                    ->visible(fn () => auth()->user()->hasRole('admin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('researchScheme.name')
                    ->label('Skema')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('leader.name')
                    ->label('Ketua Tim')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fund_amount')
                    ->label('Dana (Rp)')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'under_review' => 'info',
                        'revision_required' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tgl Pengajuan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'under_review' => 'Sedang Direview',
                        'revision_required' => 'Perlu Revisi',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('research_scheme_id')
                    ->label('Skema')
                    ->relationship('researchScheme', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn ($record) => auth()->user()->can('view_proposal')),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => static::canEdit($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => static::canDelete($record)),

                // Custom actions untuk approve/reject (admin only)
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'approved']))
                    ->visible(fn ($record) => auth()->user()->can('approve_proposal') && $record->status === 'under_review'),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'rejected']))
                    ->visible(fn ($record) => auth()->user()->can('reject_proposal') && $record->status === 'under_review'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin')),
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
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
