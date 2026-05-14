<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        $isAdmin = auth()->user()?->isAdmin() ?? false;

        return [
            Actions\Action::make('exportAll')
                ->label('Ekspor CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible($isAdmin)
                ->action(fn (): StreamedResponse => RegistrationResource::exportQuery(
                    $this->getFilteredTableQuery()
                )),

            Actions\CreateAction::make()->label('Tambah pendaftaran')->visible($isAdmin),
        ];
    }
}
