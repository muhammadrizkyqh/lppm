<?php

namespace App\Filament\Dosen\Resources;

use App\Filament\Dosen\Resources\LuaranPenelitianResource\Pages;
use App\Models\LuaranPenelitian;
use App\Models\Penelitian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class LuaranPenelitianResource extends Resource
{
    protected static ?string $model = LuaranPenelitian::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Luaran Penelitian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pilih Penelitian')
                    ->schema([
                        Forms\Components\Select::make('penelitian_id')
                            ->label('Penelitian')
                            ->options(function () {
                                return Penelitian::where('user_id', Filament::auth()->id())
                                    ->where('status', 'disetujui')
                                    ->pluck('judul', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Hanya penelitian yang sudah disetujui yang dapat diinput luarannya.'),
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

                Forms\Components\Section::make('Upload File')
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
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('penelitian', function (Builder $query) {
                    $query->where('user_id', Filament::auth()->id());
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('penelitian.judul')
                    ->label('Penelitian')
                    ->limit(40)
                    ->searchable()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('jenis_luaran')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => LuaranPenelitian::getJenisLuaranOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('judul_luaran')
                    ->label('Judul Luaran')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('media_publikasi')
                    ->label('Media Publikasi')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_terbit')
                    ->label('Tanggal Terbit')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('file_luaran')
                    ->label('File')
                    ->boolean()
                    ->trueIcon('heroicon-s-document')
                    ->falseIcon('heroicon-s-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_luaran')
                    ->label('Jenis Luaran')
                    ->options(LuaranPenelitian::getJenisLuaranOptions()),

                Tables\Filters\SelectFilter::make('penelitian_id')
                    ->label('Penelitian')
                    ->options(function () {
                        return Penelitian::where('user_id', Filament::auth()->id())
                            ->pluck('judul', 'id');
                    }),
            ])
            ->actions([
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
            'edit' => Pages\EditLuaranPenelitian::route('/{record}/edit'),
        ];
    }
}
