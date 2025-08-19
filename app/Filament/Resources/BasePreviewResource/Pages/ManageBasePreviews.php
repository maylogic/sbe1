<?php

namespace App\Filament\Resources\BasePreviewResource\Pages;

use App\Filament\Resources\BasePreviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBasePreviews extends ManageRecords
{
    protected static string $resource = BasePreviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
