<?php

namespace App\Filament\Resources\ResearchSchemeResource\Pages;

use App\Filament\Resources\ResearchSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResearchSchemes extends ListRecords
{
    protected static string $resource = ResearchSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
