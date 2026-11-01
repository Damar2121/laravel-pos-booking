<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Nomor WhatsApp / HP')
                    ->tel()
                    ->maxLength(50),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('points')
                    ->label('Poin Loyalitas')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('address')
                    ->label('Alamat Lengkap')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan Khusus')
                    ->columnSpanFull(),
            ]);
    }
}
