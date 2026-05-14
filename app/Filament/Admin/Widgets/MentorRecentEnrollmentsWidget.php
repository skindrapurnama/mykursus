<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Registration;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MentorRecentEnrollmentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Pendaftar terbaru di kursus saya';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isInstructor();
    }

    protected function getTableQuery(): Builder
    {
        return Registration::query()
            ->with(['user', 'course', 'payment'])
            ->whereHas('course.mentors', function (Builder $q): void {
                $q->where('mentors.user_id', auth()->id());
            })
            ->latest('created_at');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Tanggal')
                ->dateTime('d M Y H:i')
                ->sortable(),

            TextColumn::make('user.name')
                ->label('Siswa')
                ->description(fn (Registration $record): ?string => $record->user?->email)
                ->searchable(),

            TextColumn::make('course.title')
                ->label('Kursus')
                ->limit(35)
                ->tooltip(fn ($state): ?string => $state)
                ->searchable(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'pending' => 'Tertunda',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default => ucfirst($state),
                }),

            TextColumn::make('payment.status')
                ->label('Pembayaran')
                ->badge()
                ->color(fn (?string $state): string => match ($state) {
                    'pending' => 'warning',
                    'paid', 'approved' => 'success',
                    'rejected', 'failed' => 'danger',
                    default => 'gray',
                })
                ->formatStateUsing(fn (?string $state): string => match ($state) {
                    'pending' => 'Tertunda',
                    'paid' => 'Terbayar',
                    'approved' => 'Terbayar',
                    'rejected' => 'Ditolak',
                    'failed' => 'Gagal',
                    null, '' => 'Belum bayar',
                    default => ucfirst((string) $state),
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25])
            ->emptyStateHeading('Belum ada pendaftar')
            ->emptyStateDescription('Pendaftaran baru di kursus Anda akan tampil di sini.');
    }
}
