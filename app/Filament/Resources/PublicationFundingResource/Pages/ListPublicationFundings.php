<?php

namespace App\Filament\Resources\PublicationFundingResource\Pages;

use App\Filament\Resources\PublicationFundingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicationFundings extends ListRecords
{
    protected static string $resource = PublicationFundingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
