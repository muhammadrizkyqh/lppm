<?php

namespace App\Filament\Resources\KategoriPenelitianResource\Pages;

use App\Filament\Resources\KategoriPenelitianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriPenelitians extends ListRecords
{
    protected static string $resource = KategoriPenelitianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
