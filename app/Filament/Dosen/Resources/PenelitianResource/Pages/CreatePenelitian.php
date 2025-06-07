<?php

namespace App\Filament\Dosen\Resources\PenelitianResource\Pages;

use App\Filament\Dosen\Resources\PenelitianResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreatePenelitian extends CreateRecord
{
    protected static string $resource = PenelitianResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
