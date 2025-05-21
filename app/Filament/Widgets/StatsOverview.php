<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            now()->startOfMonth();

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now()->endOfMonth();
        
        $totalIncome = Transaction::income()
        ->whereBetween('date_transaction', [$startDate, $endDate])
        ->sum('amount');

        $totalExpense = Transaction::expense()
        ->whereBetween('date_transaction', [$startDate, $endDate])
        ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        return [
            Stat::make('Total Pemasukan', 'Rp. ' . number_format($totalIncome, 0, ',', '.'))
                ->icon('heroicon-o-arrow-up-circle')
                ->description('Pemasukan')
                ->descriptionIcon('heroicon-o-arrow-up')
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp. ' . number_format($totalExpense, 0, ',', '.'))
                ->icon('heroicon-o-arrow-down-circle')
                ->description('Pengeluaran')
                ->descriptionIcon('heroicon-o-arrow-down')
                ->color('danger'),
            Stat::make('Selisih', 'Rp. ' . number_format($balance, 0, ',', '.'))
                ->description($balance >= 0 ? 'Surplus' : 'Defisit')
                ->icon('heroicon-o-check-circle')
                ->descriptionIcon($balance >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($balance >= 0 ? 'success' : 'danger'),
        ];
    }
}
