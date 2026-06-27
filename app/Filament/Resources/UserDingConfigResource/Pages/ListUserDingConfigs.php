<?php

namespace App\Filament\Resources\UserDingConfigResource\Pages;

use App\Filament\Resources\UserDingConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserDingConfigs extends ListRecords
{
    protected static string $resource = UserDingConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
