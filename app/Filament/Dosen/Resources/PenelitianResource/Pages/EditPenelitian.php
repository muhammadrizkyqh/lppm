<?php

namespace App\Filament\Dosen\Resources\PenelitianResource\Pages;

use App\Filament\Dosen\Resources\PenelitianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenelitian extends EditRecord
{
    protected static string $resource = PenelitianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
