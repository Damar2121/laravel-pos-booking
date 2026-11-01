<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Transaksi POS Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Order::query()->latest()->limit(6))
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Faktur')
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->placeholder('Walk-in Customer'),
                TextColumn::make('cashier.name')
                    ->label('Kasir'),
                TextColumn::make('total')
                    ->label('Total Transaksi')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->weight('bold'),
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'qris' => 'info',
                        'cash' => 'success',
                        'transfer' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since(),
            ])
            ->paginated(false);
    }
}
