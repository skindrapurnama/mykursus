<?php

namespace App\Filament\Resources\MentorResource\Pages;

use App\Filament\Resources\MentorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateMentor extends CreateRecord
{
    protected static string $resource = MentorResource::class;

    protected function afterCreate(): void
    {
        User::where('id', $this->record->user_id)
            ->update(['role' => User::ROLE_INSTRUCTOR]);
    }
}
