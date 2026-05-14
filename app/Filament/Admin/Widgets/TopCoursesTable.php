<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Course;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopCoursesTable extends BaseWidget
{
    protected static ?string $heading = 'Kursus terlaris';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getTableQuery(): Builder
    {
        return Course::query()
            ->select('courses.*')
            ->selectSub(
                fn ($q) => $q->from('registrations')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('registrations.course_id', 'courses.id'),
                'registrations_count'
            )
            ->selectSub(
                fn ($q) => $q->from('registrations')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('registrations.course_id', 'courses.id')
                    ->where('registrations.status', 'approved'),
                'approved_count'
            )
            ->selectSub(
                fn ($q) => $q->from('payments')
                    ->join('registrations', 'registrations.id', '=', 'payments.registration_id')
                    ->selectRaw('COALESCE(SUM(payments.amount), 0)')
                    ->whereColumn('registrations.course_id', 'courses.id')
                    ->whereIn('payments.status', ['paid', 'approved']),
                'revenue_total'
            )
            ->orderByDesc('registrations_count')
            ->orderByDesc('revenue_total');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->label('Kursus')
                ->limit(40)
                ->tooltip(fn ($state): ?string => $state)
                ->searchable(),

            TextColumn::make('quota')
                ->label('Kuota')
                ->alignCenter()
                ->sortable(),

            TextColumn::make('registrations_count')
                ->label('Pendaftaran')
                ->alignCenter()
                ->badge()
                ->color('primary')
                ->sortable(),

            TextColumn::make('approved_count')
                ->label('Disetujui')
                ->alignCenter()
                ->badge()
                ->color('success')
                ->sortable(),

            TextColumn::make('revenue_total')
                ->label('Pendapatan')
                ->alignRight()
                ->formatStateUsing(fn ($state): string => 'Rp '.number_format((float) $state, 0, ',', '.'))
                ->sortable(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25])
            ->emptyStateHeading('Belum ada kursus')
            ->emptyStateDescription('Tambahkan kursus untuk melihat peringkat terlaris di sini.');
    }
}
