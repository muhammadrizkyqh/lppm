<?php

namespace App\Filament\Resources\ResearchSchemeResource\Pages;

use App\Filament\Resources\ResearchSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResearchScheme extends EditRecord
{
    protected static string $resource = ResearchSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
