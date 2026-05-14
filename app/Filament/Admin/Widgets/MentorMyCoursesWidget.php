<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Course;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MentorMyCoursesWidget extends BaseWidget
{
    protected static ?string $heading = 'Kursus saya';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isInstructor();
    }

    protected function getTableQuery(): Builder
    {
        return Course::query()
            ->whereHas('mentors', function (Builder $q): void {
                $q->where('mentors.user_id', auth()->id());
            })
            ->withCount([
                'registrations',
                'registrations as approved_registrations_count' => fn (Builder $q) => $q->where('status', 'approved'),
                'registrations as pending_registrations_count' => fn (Builder $q) => $q->where('status', 'pending'),
                'certificates',
            ]);
    }

    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('images')
                ->label('Gambar')
                ->disk('public')
                ->square()
                ->size(48)
                ->stacked()
                ->limit(2),

            TextColumn::make('title')
                ->label('Judul kursus')
                ->limit(40)
                ->tooltip(fn ($state): ?string => $state)
                ->searchable(),

            TextColumn::make('mentors.pivot.role')
                ->label('Peran saya')
                ->state(function (Course $record): string {
                    $mentor = $record->mentors->firstWhere('user_id', auth()->id());

                    return $mentor?->pivot?->role ?? '';
                })
                ->badge()
                ->color(fn (string $state): string => $state === 'primary' ? 'success' : 'gray')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'primary' => 'Mentor utama',
                    'co-mentor' => 'Co-mentor',
                    default => '—',
                }),

            TextColumn::make('quota')
                ->label('Kuota')
                ->alignCenter()
                ->placeholder('—'),

            TextColumn::make('registrations_count')
                ->label('Total daftar')
                ->alignCenter()
                ->badge()
                ->color('primary'),

            TextColumn::make('pending_registrations_count')
                ->label('Tertunda')
                ->alignCenter()
                ->badge()
                ->color('warning'),

            TextColumn::make('approved_registrations_count')
                ->label('Disetujui')
                ->alignCenter()
                ->badge()
                ->color('success'),

            TextColumn::make('certificates_count')
                ->label('Sertifikat')
                ->alignCenter()
                ->badge()
                ->color('info'),

            TextColumn::make('start_date')
                ->label('Mulai')
                ->date('d M Y')
                ->placeholder('—'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25])
            ->emptyStateHeading('Belum ada kursus')
            ->emptyStateDescription('Anda belum ditugaskan sebagai mentor kursus apa pun.');
    }
}
