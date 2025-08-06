<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalResource\Pages;
use App\Filament\Resources\ProposalResource\RelationManagers;
use App\Models\Proposal;
use App\Models\ResearchScheme;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Proposal';
    protected static ?string $modelLabel = 'Proposal';
    protected static ?string $pluralModelLabel = 'Proposal';
    protected static ?string $navigationGroup = 'Manajemen Proposal';

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
                            ->options(User::where('user_type', 'dosen')->where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
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

                Forms\Components\Section::make('Upload Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('proposal_documents')
                            ->label('Dokumen Proposal Lengkap')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240) // 10MB
                            ->multiple()
                            ->downloadable()
                            ->previewable()
                            ->helperText('Upload dokumen proposal dalam format PDF atau Word (maksimal 10MB)'),

                        Forms\Components\FileUpload::make('final_reports')
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
                            ->default('draft'),
                    ]),
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
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
