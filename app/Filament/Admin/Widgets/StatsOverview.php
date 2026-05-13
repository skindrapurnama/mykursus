<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $usersThisMonth = User::whereBetween('created_at', [$startOfMonth, $now])->count();
        $usersLastMonth = User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $regsThisMonth = Registration::whereBetween('created_at', [$startOfMonth, $now])->count();
        $regsLastMonth = Registration::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $revenueThisMonth = (float) Payment::whereIn('status', ['paid', 'approved'])
            ->whereBetween('paid_at', [$startOfMonth, $now])
            ->sum('amount');
        $revenueLastMonth = (float) Payment::whereIn('status', ['paid', 'approved'])
            ->whereBetween('paid_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        $regsTrend = $this->trendChart(Registration::class, 'created_at', 7);
        $revenueTrend = $this->revenueTrendChart(7);

        return [
            Stat::make('Total Siswa', number_format(User::count(), 0, ',', '.'))
                ->description($this->describeDelta($usersThisMonth, $usersLastMonth, 'siswa baru'))
                ->descriptionIcon($usersThisMonth >= $usersLastMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($usersThisMonth >= $usersLastMonth ? 'success' : 'danger'),

            Stat::make('Total Kursus', number_format(Course::count(), 0, ',', '.'))
                ->description('Kursus aktif di katalog')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Total Pendaftaran', number_format(Registration::count(), 0, ',', '.'))
                ->description($this->describeDelta($regsThisMonth, $regsLastMonth, 'pendaftaran bulan ini'))
                ->descriptionIcon($regsThisMonth >= $regsLastMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($regsTrend)
                ->color($regsThisMonth >= $regsLastMonth ? 'success' : 'warning'),

            Stat::make('Pendaftaran Tertunda', number_format(Registration::where('status', 'pending')->count(), 0, ',', '.'))
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Sertifikat', number_format(Certificate::count(), 0, ',', '.'))
                ->description('Telah diterbitkan')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),

            Stat::make('Pendapatan', 'Rp '.number_format($revenueThisMonth, 0, ',', '.'))
                ->description($this->describeRevenueDelta($revenueThisMonth, $revenueLastMonth))
                ->descriptionIcon($revenueThisMonth >= $revenueLastMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($revenueTrend)
                ->color($revenueThisMonth >= $revenueLastMonth ? 'success' : 'danger'),
        ];
    }

    private function describeDelta(int $current, int $previous, string $unit): string
    {
        if ($previous === 0 && $current === 0) {
            return 'Belum ada '.$unit.' bulan ini';
        }

        if ($previous === 0) {
            return '+'.$current.' '.$unit;
        }

        $delta = $current - $previous;
        $pct = round(($delta / max($previous, 1)) * 100);
        $sign = $delta >= 0 ? '+' : '';

        return $sign.$pct.'% vs bulan lalu';
    }

    private function describeRevenueDelta(float $current, float $previous): string
    {
        if ($previous <= 0 && $current <= 0) {
            return 'Belum ada pendapatan bulan ini';
        }

        if ($previous <= 0) {
            return 'Pendapatan baru bulan ini';
        }

        $delta = $current - $previous;
        $pct = round(($delta / max($previous, 1)) * 100);
        $sign = $delta >= 0 ? '+' : '';

        return $sign.$pct.'% vs bulan lalu';
    }

    private function trendChart(string $modelClass, string $column, int $days): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();

        $rows = $modelClass::query()
            ->selectRaw('DATE('.$column.') as d, COUNT(*) as c')
            ->where($column, '>=', $start)
            ->groupBy('d')
            ->pluck('c', 'd');

        $chart = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $chart[] = (int) ($rows[$date] ?? 0);
        }

        return $chart;
    }

    private function revenueTrendChart(int $days): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();

        $rows = Payment::query()
            ->whereIn('status', ['paid', 'approved'])
            ->selectRaw('DATE(paid_at) as d, SUM(amount) as s')
            ->where('paid_at', '>=', $start)
            ->groupBy('d')
            ->pluck('s', 'd');

        $chart = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $chart[] = (float) ($rows[$date] ?? 0);
        }

        return $chart;
    }
}
