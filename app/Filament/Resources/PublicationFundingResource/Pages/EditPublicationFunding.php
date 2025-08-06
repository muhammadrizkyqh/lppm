<?php

namespace App\Filament\Resources\PublicationFundingResource\Pages;

use App\Filament\Resources\PublicationFundingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPublicationFunding extends EditRecord
{
    protected static string $resource = PublicationFundingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
