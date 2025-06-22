<?php

namespace App\Filament\Resources\PostHistoryResource\Pages;

use App\Filament\Resources\PostHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPostHistories extends ListRecords
{
    protected static string $resource = PostHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
