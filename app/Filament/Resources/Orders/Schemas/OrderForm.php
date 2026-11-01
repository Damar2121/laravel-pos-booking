<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_number')
                    ->label('Nomor Faktur / Invoice')
                    ->required()
                    ->default(fn () => 'INV-'.date('Ymd').'-'.strtoupper(Str::random(5)))
                    ->unique(ignoreRecord: true),
                Select::make('customer_id')
                    ->label('Pelanggan (Opsional)')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('user_id')
                    ->label('Kasir / Petugas')
                    ->relationship('cashier', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->id())
                    ->required(),
                Select::make('booking_id')
                    ->label('Terkait Jadwal Booking')
                    ->relationship('booking', 'booking_code')
                    ->searchable()
                    ->preload(),
                Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Tunai (Cash)',
                        'qris' => 'QRIS (E-Wallet & Mobile Banking)',
                        'transfer' => 'Bank Transfer',
                        'card' => 'Debit / Kartu Kredit',
                    ])
                    ->default('cash')
                    ->required(),
                Select::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'Lunas (Paid)',
                        'unpaid' => 'Belum Lunas (Unpaid)',
                        'refunded' => 'Dikembalikan (Refunded)',
                    ])
                    ->default('paid')
                    ->required(),
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('discount')
                    ->label('Diskon')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('tax')
                    ->label('Pajak (Tax)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('total')
                    ->label('Total Transaksi')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('paid_amount')
                    ->label('Nominal Dibayar')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('change_amount')
                    ->label('Kembalian')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                Repeater::make('items')
                    ->relationship('items')
                    ->label('Daftar Produk / Layanan')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $p = Product::find($state);
                                    if ($p) {
                                        $set('product_name', $p->name);
                                        $set('unit_price', $p->price);
                                        $set('subtotal', $p->price * 1);
                                    }
                                }
                            }),
                        TextInput::make('product_name')
                            ->label('Nama Item')
                            ->required(),
                        TextInput::make('unit_price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Qty')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $unitPrice = (float) ($get('unit_price') ?? 0);
                                $set('subtotal', $unitPrice * (int) $state);
                            }),
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan Transaksi')
                    ->columnSpanFull(),
            ]);
    }
}
