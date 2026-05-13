<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan 12 bulan terakhir';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected function getData(): array
    {
        $start = Carbon::now()->subMonthsNoOverflow(11)->startOfMonth();

        $rows = Payment::query()
            ->whereIn('status', ['paid', 'approved'])
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $values = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonthsNoOverflow($i);
            $key = $month->format('Y-m');
            $labels[] = $month->translatedFormat('M Y');
            $values[] = (float) ($rows[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $values,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.55)',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'callback' => 'function(value){return "Rp " + value.toLocaleString("id-ID");}',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ];
    }
}
