<?php

namespace App\Filament\Resources\SalesRuleResource\Pages;

use App\Filament\Resources\SalesRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSalesRules extends ManageRecords
{
    protected static string $resource = SalesRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
