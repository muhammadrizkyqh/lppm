<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResearchSchemeResource\Pages;
use App\Filament\Resources\ResearchSchemeResource\RelationManagers;
use App\Models\ResearchScheme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResearchSchemeResource extends Resource
{
    protected static ?string $model = ResearchScheme::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Skema Penelitian';
    protected static ?string $modelLabel = 'Skema Penelitian';
    protected static ?string $pluralModelLabel = 'Skema Penelitian';
    protected static ?string $navigationGroup = 'MASTER DATA';
    protected static ?int $navigationSort = 1;

    // Permission checking - Admin only
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('create_research_scheme');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_research_scheme');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit_research_scheme');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_research_scheme');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('create_research_scheme');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Skema')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(3),

                Forms\Components\Select::make('type')
                    ->label('Jenis')
                    ->options([
                        'penelitian' => 'Penelitian',
                        'pengabdian' => 'Pengabdian Masyarakat',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('default_fund_amount')
                    ->label('Dana Hibah Default (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\DatePicker::make('registration_start_date')
                    ->label('Tanggal Mulai Pendaftaran')
                    ->required(),

                Forms\Components\DatePicker::make('registration_end_date')
                    ->label('Tanggal Selesai Pendaftaran')
                    ->required()
                    ->after('registration_start_date'),

                Forms\Components\CheckboxList::make('expected_outputs')
                    ->label('Jenis Luaran yang Diharapkan')
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
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Skema')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'penelitian' => 'info',
                        'pengabdian' => 'success',
                    }),

                Tables\Columns\TextColumn::make('default_fund_amount')
                    ->label('Dana (Rp)')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_start_date')
                    ->label('Mulai Pendaftaran')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_end_date')
                    ->label('Selesai Pendaftaran')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'penelitian' => 'Penelitian',
                        'pengabdian' => 'Pengabdian Masyarakat',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            'index' => Pages\ListResearchSchemes::route('/'),
            'create' => Pages\CreateResearchScheme::route('/create'),
            'edit' => Pages\EditResearchScheme::route('/{record}/edit'),
        ];
    }
}
