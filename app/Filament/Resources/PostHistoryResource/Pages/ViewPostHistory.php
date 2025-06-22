<?php

namespace App\Filament\Resources\PostHistoryResource\Pages;

use App\Filament\Resources\PostHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPostHistory extends ViewRecord
{
    protected static string $resource = PostHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
