<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use App\Models\Proposal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Review Proposal';
    protected static ?string $modelLabel = 'Review';
    protected static ?string $pluralModelLabel = 'Review';
    protected static ?string $navigationGroup = 'Review & Penilaian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Assignment Information')
                    ->schema([
                        Forms\Components\Select::make('proposal_id')
                            ->label('Proposal')
                            ->options(Proposal::where('status', 'submitted')->with('researchScheme')->get()->pluck('title', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('reviewer_id')
                            ->label('Reviewer')
                            ->options(User::whereHas('roles', function($q) { $q->where('name', 'reviewer'); })->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('assigned_by')
                            ->label('Ditugaskan Oleh')
                            ->options(User::whereHas('roles', function($q) { $q->where('name', 'admin'); })->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required(),

                        Forms\Components\DateTimePicker::make('assigned_at')
                            ->label('Tanggal Penugasan')
                            ->default(now())
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Penilaian Kriteria')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('relevance_score')
                                    ->label('Skor Relevansi (1-5)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(5)
                                    ->step(1),

                                Forms\Components\Textarea::make('relevance_comment')
                                    ->label('Komentar Relevansi')
                                    ->rows(2),

                                Forms\Components\TextInput::make('methodology_score')
                                    ->label('Skor Metodologi (1-5)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(5)
                                    ->step(1),

                                Forms\Components\Textarea::make('methodology_comment')
                                    ->label('Komentar Metodologi')
                                    ->rows(2),

                                Forms\Components\TextInput::make('originality_score')
                                    ->label('Skor Orisinalitas (1-5)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(5)
                                    ->step(1),

                                Forms\Components\Textarea::make('originality_comment')
                                    ->label('Komentar Orisinalitas')
                                    ->rows(2),

                                Forms\Components\TextInput::make('feasibility_score')
                                    ->label('Skor Kelayakan (1-5)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(5)
                                    ->step(1),

                                Forms\Components\Textarea::make('feasibility_comment')
                                    ->label('Komentar Kelayakan')
                                    ->rows(2),
                            ]),

                        Forms\Components\TextInput::make('total_score')
                            ->label('Total Skor')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Forms\Components\Section::make('Kesimpulan Review')
                    ->schema([
                        Forms\Components\Textarea::make('general_comment')
                            ->label('Komentar Umum')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('recommendation')
                            ->label('Rekomendasi')
                            ->options([
                                'approved_without_revision' => 'Disetujui Tanpa Revisi',
                                'approved_with_revision' => 'Disetujui Dengan Revisi',
                                'rejected' => 'Ditolak',
                            ])
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Review')
                            ->options([
                                'assigned' => 'Ditugaskan',
                                'in_progress' => 'Sedang Berlangsung',
                                'completed' => 'Selesai',
                            ])
                            ->default('assigned'),
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
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_score')
                    ->label('Total Skor')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function (Review $record) {
                        return ($record->relevance_score + $record->methodology_score + $record->originality_score + $record->feasibility_score) ?: null;
                    }),

                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Rekomendasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved_without_revision' => 'success',
                        'approved_with_revision' => 'warning',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved_without_revision' => 'Disetujui Tanpa Revisi',
                        'approved_with_revision' => 'Disetujui Dengan Revisi',
                        'rejected' => 'Ditolak',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'assigned' => 'Ditugaskan',
                        'in_progress' => 'Berlangsung',
                        'completed' => 'Selesai',
                    }),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Tgl Penugasan')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tgl Submit')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'assigned' => 'Ditugaskan',
                        'in_progress' => 'Berlangsung',
                        'completed' => 'Selesai',
                    ]),

                Tables\Filters\SelectFilter::make('recommendation')
                    ->label('Rekomendasi')
                    ->options([
                        'approved_without_revision' => 'Disetujui Tanpa Revisi',
                        'approved_with_revision' => 'Disetujui Dengan Revisi',
                        'rejected' => 'Ditolak',
                    ]),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
