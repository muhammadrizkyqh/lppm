<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalTeamMemberResource\Pages;
use App\Filament\Resources\ProposalTeamMemberResource\RelationManagers;
use App\Models\ProposalTeamMember;
use App\Models\Proposal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProposalTeamMemberResource extends Resource
{
    protected static ?string $model = ProposalTeamMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Tim Anggota';

    protected static ?string $modelLabel = 'Anggota Tim Proposal';

    protected static ?string $pluralModelLabel = 'Tim Proposal';

    protected static ?string $navigationGroup = 'MANAJEMEN PROPOSAL';

    protected static ?int $navigationSort = 2;

    // Permission checking for team member management
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('manage_team_members');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('manage_team_members');
    }

    public static function canEdit($record): bool
    {
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        return $record->proposal->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        return $record->proposal->user_id === auth()->id();
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
                Forms\Components\Section::make('Informasi Tim')
                    ->schema([
                        Forms\Components\Select::make('proposal_id')
                            ->label('Proposal')
                            ->options(function () {
                                return Proposal::with('user')
                                    ->whereIn('status', ['draft', 'submitted', 'under_review'])
                                    ->get()
                                    ->mapWithKeys(function ($proposal) {
                                        return [$proposal->id => $proposal->title . ' (' . $proposal->user->name . ')'];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Reset other fields when proposal changes
                                $set('user_id', null);
                                $set('name', null);
                                $set('nidn', null);
                                $set('nim', null);
                            }),

                        Forms\Components\Select::make('member_type')
                            ->label('Jenis Anggota')
                            ->options([
                                'dosen' => 'Dosen',
                                'mahasiswa' => 'Mahasiswa',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Reset fields when member type changes
                                $set('user_id', null);
                                $set('name', null);
                                $set('nidn', null);
                                $set('nim', null);
                            }),

                        Forms\Components\Select::make('role')
                            ->label('Peran dalam Tim')
                            ->options([
                                'ketua' => 'Ketua',
                                'anggota' => 'Anggota',
                            ])
                            ->required()
                            ->default('anggota'),
                    ]),

                Forms\Components\Section::make('Data Anggota')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pilih Dosen')
                            ->options(function () {
                                return User::whereHas('roles', function ($query) {
                                    $query->where('name', 'dosen');
                                })->pluck('name', 'id');
                            })
                            ->searchable()
                            ->reactive()
                            ->visible(fn (callable $get) => $get('member_type') === 'dosen')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('name', $user->name);
                                        $set('nidn', $user->nidn);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (callable $get) => $get('member_type') === 'dosen' && $get('user_id')),

                        Forms\Components\TextInput::make('nidn')
                            ->label('NIDN')
                            ->visible(fn (callable $get) => $get('member_type') === 'dosen')
                            ->disabled(fn (callable $get) => $get('member_type') === 'dosen' && $get('user_id'))
                            ->maxLength(20),

                        Forms\Components\TextInput::make('nim')
                            ->label('NIM')
                            ->visible(fn (callable $get) => $get('member_type') === 'mahasiswa')
                            ->maxLength(20)
                            ->required(fn (callable $get) => $get('member_type') === 'mahasiswa'),
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
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('member_type')
                    ->label('Jenis')
                    ->colors([
                        'success' => 'dosen',
                        'primary' => 'mahasiswa',
                    ])
                    ->icons([
                        'heroicon-o-academic-cap' => 'dosen',
                        'heroicon-o-user' => 'mahasiswa',
                    ]),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Peran')
                    ->colors([
                        'warning' => 'ketua',
                        'secondary' => 'anggota',
                    ])
                    ->icons([
                        'heroicon-o-star' => 'ketua',
                        'heroicon-o-user-group' => 'anggota',
                    ]),

                Tables\Columns\TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('proposal.status')
                    ->label('Status Proposal')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'under_review' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'revision_required' => 'warning',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('member_type')
                    ->label('Jenis Anggota')
                    ->options([
                        'dosen' => 'Dosen',
                        'mahasiswa' => 'Mahasiswa',
                    ]),

                Tables\Filters\SelectFilter::make('role')
                    ->label('Peran')
                    ->options([
                        'ketua' => 'Ketua',
                        'anggota' => 'Anggota',
                    ]),

                Tables\Filters\SelectFilter::make('proposal_status')
                    ->label('Status Proposal')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'revision_required' => 'Revision Required',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'], function ($query) use ($data) {
                            $query->whereHas('proposal', function ($query) use ($data) {
                                $query->where('status', $data['value']);
                            });
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProposalTeamMembers::route('/'),
            'create' => Pages\CreateProposalTeamMember::route('/create'),
            'edit' => Pages\EditProposalTeamMember::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
