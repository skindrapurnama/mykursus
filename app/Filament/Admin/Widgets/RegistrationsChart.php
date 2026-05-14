<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Registration;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RegistrationsChart extends ChartWidget
{
    protected static ?string $heading = 'Pendaftaran 30 hari terakhir';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    public ?string $filter = '30';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 hari',
            '30' => '30 hari',
            '90' => '90 hari',
        ];
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? 30);
        $start = Carbon::now()->subDays($days - 1)->startOfDay();

        $rows = Registration::query()
            ->selectRaw('DATE(created_at) as d, status, COUNT(*) as c')
            ->where('created_at', '>=', $start)
            ->groupBy('d', 'status')
            ->get()
            ->groupBy('status');

        $labels = [];
        $approved = [];
        $pending = [];
        $rejected = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();
            $labels[] = $date->translatedFormat($days <= 14 ? 'd M' : 'd/m');

            $approved[] = (int) optional(($rows['approved'] ?? collect())->firstWhere('d', $key))->c;
            $pending[] = (int) optional(($rows['pending'] ?? collect())->firstWhere('d', $key))->c;
            $rejected[] = (int) optional(($rows['rejected'] ?? collect())->firstWhere('d', $key))->c;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Disetujui',
                    'data' => $approved,
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Tertunda',
                    'data' => $pending,
                    'borderColor' => '#d97706',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Ditolak',
                    'data' => $rejected,
                    'borderColor' => '#dc2626',
                    'backgroundColor' => 'rgba(220, 38, 38, 0.10)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
