<?php

namespace App\Filament\Resources\UserStockResource\Pages;

use App\Filament\Resources\UserStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserStocks extends ListRecords
{
    protected static string $resource = UserStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
