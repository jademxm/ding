<?php

namespace App\Filament\Resources\UserStockResource\Pages;

use App\Filament\Resources\UserStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserStock extends EditRecord
{
    protected static string $resource = UserStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
