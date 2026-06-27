<?php

namespace App\Filament\Admin\Resources\UserDingConfigResource\Pages;

use App\Filament\Admin\Resources\UserDingConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserDingConfig extends EditRecord
{
    protected static string $resource = UserDingConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
