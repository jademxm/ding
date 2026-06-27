<?php

namespace App\Filament\Admin\Resources\UserStockResource\Pages;

use App\Filament\Admin\Resources\UserStockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserStock extends CreateRecord
{
    protected static string $resource = UserStockResource::class;
}
