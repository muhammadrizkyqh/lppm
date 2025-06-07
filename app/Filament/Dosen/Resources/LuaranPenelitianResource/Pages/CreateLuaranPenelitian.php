<?php

namespace App\Filament\Dosen\Resources\LuaranPenelitianResource\Pages;

use App\Filament\Dosen\Resources\LuaranPenelitianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLuaranPenelitian extends CreateRecord
{
    protected static string $resource = LuaranPenelitianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
