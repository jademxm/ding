<?php

namespace App\Filament\Admin\Resources\UserStockResource\Pages;

use App\Filament\Admin\Resources\UserStockResource;
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
