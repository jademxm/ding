<?php

namespace App\Filament\App\Resources\MyStockResource\Pages;

use App\Filament\App\Resources\MyStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserStocks extends ListRecords
{
    protected static string $resource = MyStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
