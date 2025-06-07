<?php

namespace App\Filament\Resources\KategoriPenelitianResource\Pages;

use App\Filament\Resources\KategoriPenelitianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriPenelitian extends EditRecord
{
    protected static string $resource = KategoriPenelitianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
