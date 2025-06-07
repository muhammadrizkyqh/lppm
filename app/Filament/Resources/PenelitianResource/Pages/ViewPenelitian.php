<?php

namespace App\Filament\Resources\PenelitianResource\Pages;

use App\Filament\Resources\PenelitianResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPenelitian extends ViewRecord
{
    protected static string $resource = PenelitianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
