<?php

namespace App\Filament\Resources\LuaranPenelitianResource\Pages;

use App\Filament\Resources\LuaranPenelitianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLuaranPenelitians extends ListRecords
{
    protected static string $resource = LuaranPenelitianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
