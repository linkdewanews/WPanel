<?php

namespace App\Filament\Resources\PostHistoryResource\Pages;

use App\Filament\Resources\PostHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPostHistory extends EditRecord
{
    protected static string $resource = PostHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
