<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserRegistrationsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'User Registrations';

    protected function getData(): array
    {
        // Use database-agnostic date formatting
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $dateFormat = "strftime('%Y-%m', created_at)";
        } else {
            // MySQL, PostgreSQL, etc.
            $dateFormat = "DATE_FORMAT(created_at, '%Y-%m')";
        }

        $data = \App\Models\User::selectRaw("{$dateFormat} as month, count(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'User Registrations',
                    'data' => array_values($data),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
