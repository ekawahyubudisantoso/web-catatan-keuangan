<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WidgetsPemasukanChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected static ?string $heading = 'Pemasukan';
    protected static string $color = 'success';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            now()->startOfMonth();

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now()->endOfMonth();

        $data = Trend::query(Transaction::Income())
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
