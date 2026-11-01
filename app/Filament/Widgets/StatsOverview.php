<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalOrdersToday = Order::whereDate('created_at', today())->count();

        $todayBookings = Booking::whereDate('booking_date', today())
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->count();

        $lowStockCount = Product::where('type', 'goods')
            ->where('stock', '<=', 10)
            ->count();

        return [
            Stat::make('Omset Penjualan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Dari ' . $totalOrdersToday . ' transaksi lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Booking Aktif Hari Ini', $todayBookings . ' Jadwal')
                ->description('Reservasi terkonfirmasi & berjalan')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Pelanggan Terdaftar', Customer::count() . ' Member')
                ->description('Total customer loyal')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Produk Menipis (<=10)', $lowStockCount . ' Produk')
                ->description('Perlu restok barang segera')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),
        ];
    }
}
