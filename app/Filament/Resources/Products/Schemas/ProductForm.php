<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->default(fn () => 'SKU-' . strtoupper(Str::random(6)))
                    ->unique(ignoreRecord: true),
                TextInput::make('barcode')
                    ->label('Barcode / EAN')
                    ->maxLength(50),
                TextInput::make('price')
                    ->label('Harga Jual')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('cost_price')
                    ->label('Harga Modal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                TextInput::make('stock')
                    ->label('Stok')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Untuk produk tipe Jasa, stok dapat diisi jumlah besar (misal 999).'),
                Select::make('type')
                    ->label('Tipe Produk')
                    ->options([
                        'goods' => 'Barang Fisik (Retail / FnB)',
                        'service' => 'Jasa / Layanan (Booking / Service)',
                    ])
                    ->default('goods')
                    ->required(),
                FileUpload::make('image')
                    ->label('Foto Produk')
                    ->image()
                    ->directory('products')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
