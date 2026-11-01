<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('booking_code')
                    ->label('Kode Booking')
                    ->required()
                    ->default(fn () => 'BKG-'.date('Ymd').'-'.strtoupper(Str::random(4)))
                    ->unique(ignoreRecord: true),
                Select::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('product_id')
                    ->label('Layanan Jasa / Service')
                    ->relationship('product', 'name', fn ($query) => $query->where('type', 'service'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('total_price', $product->price);
                            }
                        }
                    }),
                Select::make('user_id')
                    ->label('Petugas / Terapis / Staff PIC')
                    ->relationship('staff', 'name')
                    ->searchable()
                    ->preload(),
                DatePicker::make('booking_date')
                    ->label('Tanggal Booking')
                    ->default(now())
                    ->required(),
                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required(),
                TimePicker::make('end_time')
                    ->label('Jam Selesai'),
                TextInput::make('total_price')
                    ->label('Biaya Layanan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                Select::make('status')
                    ->label('Status Reservasi')
                    ->options([
                        'pending' => 'Pending (Menunggu)',
                        'confirmed' => 'Confirmed (Terkonfirmasi)',
                        'in_progress' => 'In Progress (Sedang Berjalan)',
                        'completed' => 'Completed (Selesai)',
                        'cancelled' => 'Cancelled (Dibatalkan)',
                    ])
                    ->default('confirmed')
                    ->required(),
                Textarea::make('notes')
                    ->label('Catatan Permintaan Khusus')
                    ->columnSpanFull(),
            ]);
    }
}
