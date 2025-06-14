<?php

namespace App\Filament\Resources\TopBannerResource\Pages;

use App\Filament\Resources\TopBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTopBanners extends ManageRecords
{
    protected static string $resource = TopBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
