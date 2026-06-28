<?php

namespace App\Filament\App\Resources\MyStockResource\Pages;

use App\Filament\App\Resources\MyStockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserStock extends CreateRecord
{
    protected static string $resource = MyStockResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
