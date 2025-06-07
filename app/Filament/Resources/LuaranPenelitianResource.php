<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LuaranPenelitianResource\Pages;
use App\Models\LuaranPenelitian;
use App\Models\Penelitian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LuaranPenelitianResource extends Resource
{
    protected static ?string $model = LuaranPenelitian::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Luaran Penelitian';

    protected static ?string $navigationGroup = 'Manajemen Penelitian';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penelitian')
                    ->schema([
                        Forms\Components\Select::make('penelitian_id')
                            ->label('Penelitian')
                            ->relationship('penelitian', 'judul')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull()
                            ->getOptionLabelFromRecordUsing(fn (Penelitian $record): string => "{$record->judul} - {$record->user->name}"),
                    ]),

                Forms\Components\Section::make('Detail Luaran')
                    ->schema([
                        Forms\Components\Select::make('jenis_luaran')
                            ->label('Jenis Luaran')
                            ->options(LuaranPenelitian::getJenisLuaranOptions())
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('tanggal_terbit')
                            ->label('Tanggal Terbit')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('judul_luaran')
                            ->label('Judul Luaran')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('media_publikasi')
                            ->label('Media Publikasi')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Contoh: Jurnal ABC, Prosiding Seminar XYZ, Penerbit DEF'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('File Luaran')
                    ->schema([
                        Forms\Components\FileUpload::make('file_luaran')
                            ->label('Upload File Luaran')
                            ->directory('penelitian/luaran')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240) // 10MB
                            ->columnSpanFull()
                            ->helperText('Format yang diterima: PDF, JPG, PNG. Maksimal 10MB.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('penelitian.user.name')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penelitian.judul')
                    ->label('Penelitian')
                    ->limit(30)
                    ->searchable()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('jenis_luaran')
                    ->label('Jenis Luaran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => LuaranPenelitian::getJenisLuaranOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('judul_luaran')
                    ->label('Judul Luaran')
                    ->limit(40)
                    ->searchable()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('media_publikasi')
                    ->label('Media Publikasi')
                    ->limit(25)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_terbit')
                    ->label('Tanggal Terbit')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('file_luaran')
                    ->label('File')
                    ->boolean()
                    ->trueIcon('heroicon-s-document')
                    ->falseIcon('heroicon-s-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_luaran')
                    ->label('Jenis Luaran')
                    ->options(LuaranPenelitian::getJenisLuaranOptions()),

                Tables\Filters\Filter::make('has_file')
                    ->label('Memiliki File')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('file_luaran')),

                Tables\Filters\Filter::make('tanggal_terbit')
                    ->form([
                        Forms\Components\DatePicker::make('terbit_dari')
                            ->label('Terbit Dari'),
                        Forms\Components\DatePicker::make('terbit_sampai')
                            ->label('Terbit Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['terbit_dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_terbit', '>=', $date),
                            )
                            ->when(
                                $data['terbit_sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_terbit', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->visible(fn (LuaranPenelitian $record): bool => !empty($record->file_luaran))
                    ->url(fn (LuaranPenelitian $record): string => asset('storage/' . $record->file_luaran))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListLuaranPenelitians::route('/'),
            'create' => Pages\CreateLuaranPenelitian::route('/create'),
            // 'view' => Pages\ViewLuaranPenelitian::route('/{record}'),
            'edit' => Pages\EditLuaranPenelitian::route('/{record}/edit'),
        ];
    }
}
