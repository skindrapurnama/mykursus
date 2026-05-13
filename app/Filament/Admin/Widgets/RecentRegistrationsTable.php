<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Registration;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentRegistrationsTable extends BaseWidget
{
    protected static ?string $heading = 'Pendaftaran terbaru';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Registration::query()
            ->with(['user', 'course', 'payment'])
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
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Registration $record): bool => $record->status === 'pending')
                    ->action(fn (Registration $record) => $record->update(['status' => 'approved'])),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Registration $record): bool => $record->status === 'pending')
                    ->action(fn (Registration $record) => $record->update(['status' => 'rejected'])),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25])
            ->emptyStateHeading('Belum ada pendaftaran')
            ->emptyStateDescription('Pendaftaran baru akan tampil di sini.');
    }
}
