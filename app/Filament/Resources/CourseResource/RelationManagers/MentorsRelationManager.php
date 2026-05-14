<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\Mentor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MentorsRelationManager extends RelationManager
{
    protected static string $relationship = 'mentors';

    protected static ?string $title = 'Mentor kursus';

    protected static ?string $modelLabel = 'Mentor';

    protected static ?string $pluralModelLabel = 'Mentor';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('role')
                    ->label('Peran')
                    ->options([
                        'primary' => 'Mentor utama',
                        'co-mentor' => 'Co-mentor',
                    ])
                    ->native(false)
                    ->required()
                    ->default('primary'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->size(40),

                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Peran')
                    ->state(fn (Mentor $record): string => $record->pivot?->role ?? '')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'primary' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'primary' => 'Mentor utama',
                        'co-mentor' => 'Co-mentor',
                        default => ucfirst($state),
                    }),

                TextColumn::make('expertise')
                    ->label('Keahlian')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->color('primary'),

                TextColumn::make('years_experience')
                    ->label('Pengalaman')
                    ->suffix(' tahun')
                    ->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Tautkan mentor')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['bio'])
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->getOptionLabelFromRecordUsing(fn (Mentor $record): string => $record->user?->name ?? '—'),
                        Forms\Components\Select::make('role')
                            ->label('Peran')
                            ->options([
                                'primary' => 'Mentor utama',
                                'co-mentor' => 'Co-mentor',
                            ])
                            ->native(false)
                            ->required()
                            ->default('primary'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah peran'),
                Tables\Actions\DetachAction::make()->label('Lepas'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()->label('Lepas terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada mentor')
            ->emptyStateDescription('Tautkan mentor untuk kursus ini.');
    }
}
