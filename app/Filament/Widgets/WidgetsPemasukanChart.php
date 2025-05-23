<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Flowframe\Trend\Trend;
use App\Models\Transaction;
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
        $startDate = !empty($this->filters['startDate']) ? Carbon::parse($this->filters['startDate']) : now()->startOfMonth();
        $endDate = !empty($this->filters['endDate']) ? Carbon::parse($this->filters['endDate']) : now()->endOfMonth();

        $data = Trend::query(Transaction::Income())
            ->dateColumn('date_transaction')
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
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                    'borderColor' => '#4CAF50',
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
