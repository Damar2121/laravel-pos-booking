<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT COLUMN: PRODUCT CATALOG & FILTER --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">
            {{-- Top Controls: Search & Category Pills --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-800 space-y-3">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk berdasarkan nama, SKU, atau barcode..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none dark:text-gray-100"
                    />
                    <div class="absolute left-3 top-3 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                {{-- Category Badges --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <button 
                        wire:click="selectCategory('all')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === 'all' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}"
                    >
                        Semua Kategori
                    </button>
                    @foreach($this->categories as $category)
                        <button 
                            wire:click="selectCategory('{{ $category->id }}')"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ (string)$selectedCategory === (string)$category->id ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($this->products as $product)
                    <div 
                        wire:click="addToCart({{ $product->id }})"
                        class="group relative bg-white dark:bg-gray-900 rounded-xl p-3 shadow-sm border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between"
                    >
                        <div>
                            {{-- Type & Stock Badge --}}
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $product->type === 'service' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' }}">
                                    {{ $product->type === 'service' ? 'Layanan/Jasa' : 'Barang' }}
                                </span>
                                @if($product->type === 'goods')
                                    <span class="text-[10px] font-semibold {{ $product->stock <= 5 ? 'text-rose-600' : 'text-gray-500' }}">
                                        Stok: {{ $product->stock }}
                                    </span>
                                @endif
                            </div>

                            {{-- Product Image / Placeholder --}}
                            <div class="w-full h-28 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden mb-2">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                @else
                                    <div class="text-3xl text-gray-400">
                                        @if($product->type === 'service')
                                            ✂️
                                        @elseif(str_contains(strtolower($product->name), 'coffee') || str_contains(strtolower($product->name), 'latte') || str_contains(strtolower($product->name), 'macchiato'))
                                            ☕
                                        @elseif(str_contains(strtolower($product->name), 'burger') || str_contains(strtolower($product->name), 'croissant'))
                                            🥐
                                        @else
                                            🛍️
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 leading-snug group-hover:text-primary-600 transition-colors">
                                {{ $product->name }}
                            </h4>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $product->sku }}</p>
                        </div>

                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            <span class="p-1.5 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400 group-hover:bg-primary-600 group-hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 bg-white dark:bg-gray-900 rounded-xl border border-dashed border-gray-300 dark:border-gray-800">
                        <p class="text-base font-semibold">Tidak ada produk ditemukan.</p>
                        <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci pencarian atau kategori lain.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT COLUMN: CART & CHECKOUT --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col h-[calc(100vh-140px)] sticky top-6">
                
                {{-- Customer & Booking Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-800 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Keranjang Transaksi
                        </h3>
                        @if(count($cart) > 0)
                            <button wire:click="clearCart" class="text-xs text-rose-500 hover:text-rose-700 font-medium transition-colors">
                                Kosongkan
                            </button>
                        @endif
                    </div>

                    {{-- Customer Selector --}}
                    <div>
                        <select 
                            wire:model.live="customerId"
                            class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 py-1.5 px-2.5 focus:ring-1 focus:ring-primary-500"
                        >
                            <option value="">-- Walk-in Customer (Umum) --</option>
                            @foreach($this->customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone ?? 'Tanpa No. HP' }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Quick Booking Importer --}}
                    @if($this->pendingBookings->isNotEmpty())
                        <div>
                            <select 
                                wire:change="loadBooking($event.target.value)"
                                class="w-full text-xs rounded-lg border border-purple-300 dark:border-purple-800 bg-purple-50 dark:bg-purple-950/40 text-purple-900 dark:text-purple-200 py-1.5 px-2.5 focus:ring-1 focus:ring-purple-500"
                            >
                                <option value="">🗓️ Impor dari Jadwal Booking...</option>
                                @foreach($this->pendingBookings as $bkg)
                                    <option value="{{ $bkg->id }}">
                                        {{ $bkg->booking_code }} - {{ $bkg->customer->name }} ({{ substr($bkg->start_time, 0, 5) }} WIB)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                {{-- Scrollable Cart Items --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($cart as $item)
                        <div class="pt-3 first:pt-0 flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h5 class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ $item['name'] }}</h5>
                                <p class="text-[11px] text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>

                            {{-- Quantity Stepper --}}
                            <div class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                                <button 
                                    wire:click="updateQuantity({{ $item['id'] }}, -1)"
                                    class="w-6 h-6 rounded flex items-center justify-center bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-200 hover:bg-gray-200 text-xs font-bold shadow-xs transition-colors"
                                >
                                    -
                                </button>
                                <span class="w-6 text-center text-xs font-bold text-gray-800 dark:text-gray-100">
                                    {{ $item['quantity'] }}
                                </span>
                                <button 
                                    wire:click="updateQuantity({{ $item['id'] }}, 1)"
                                    class="w-6 h-6 rounded flex items-center justify-center bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-200 hover:bg-gray-200 text-xs font-bold shadow-xs transition-colors"
                                >
                                    +
                                </button>
                            </div>

                            <div class="text-right min-w-[70px]">
                                <span class="text-xs font-bold text-gray-900 dark:text-gray-100 block">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </span>
                                <button 
                                    wire:click="removeFromCart({{ $item['id'] }})"
                                    class="text-[10px] text-rose-500 hover:underline mt-0.5"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-gray-400 py-10">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <p class="text-xs font-medium">Keranjang masih kosong</p>
                            <p class="text-[11px] text-gray-400">Klik item di katalog untuk menambahkan.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Order Summary & Checkout Trigger --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-800 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-200">
                            Rp {{ number_format($this->subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Discount Input --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-gray-600 dark:text-gray-400">Diskon (Rp)</span>
                        <input 
                            type="number" 
                            wire:model.live.debounce.300ms="discount"
                            class="w-24 text-right py-1 px-2 text-xs rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="0"
                        />
                    </div>

                    {{-- Tax Selection --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-gray-600 dark:text-gray-400">Pajak (PPN)</span>
                        <select 
                            wire:model.live="taxPercent"
                            class="text-xs py-1 px-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="0">Bebas Pajak (0%)</option>
                            <option value="11">PPN 11%</option>
                        </select>
                    </div>

                    {{-- Grand Total --}}
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700 flex justify-between items-baseline">
                        <span class="font-bold text-sm text-gray-900 dark:text-gray-100">Grand Total</span>
                        <span class="font-extrabold text-lg text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Checkout Button --}}
                    <button 
                        wire:click="openCheckoutModal"
                        @disabled(count($cart) === 0)
                        class="w-full py-3 mt-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Bayar Sekarang (F9)
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: CHECKOUT & PEMBAYARAN --}}
    @if($showCheckoutModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800 space-y-5 animate-in fade-in duration-200">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span>💳</span> Pembayaran Transaksi
                    </h3>
                    <button wire:click="closeCheckoutModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Total Display --}}
                <div class="bg-primary-50 dark:bg-primary-950/50 p-4 rounded-xl text-center">
                    <p class="text-xs text-primary-700 dark:text-primary-300 font-medium">Total Tagihan Yang Harus Dibayar</p>
                    <h2 class="text-3xl font-extrabold text-primary-600 dark:text-primary-400 mt-1">
                        Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                    </h2>
                </div>

                {{-- Payment Method Selection --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button 
                            type="button" 
                            wire:click="$set('paymentMethod', 'cash')"
                            class="py-2.5 px-3 rounded-xl border text-xs font-bold flex flex-col items-center gap-1 transition-all {{ $paymentMethod === 'cash' ? 'border-primary-600 bg-primary-50 dark:bg-primary-950 text-primary-600' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300' }}"
                        >
                            <span class="text-xl">💵</span> Tunai (Cash)
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('paymentMethod', 'qris')"
                            class="py-2.5 px-3 rounded-xl border text-xs font-bold flex flex-col items-center gap-1 transition-all {{ $paymentMethod === 'qris' ? 'border-primary-600 bg-primary-50 dark:bg-primary-950 text-primary-600' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300' }}"
                        >
                            <span class="text-xl">📱</span> QRIS Instan
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('paymentMethod', 'transfer')"
                            class="py-2.5 px-3 rounded-xl border text-xs font-bold flex flex-col items-center gap-1 transition-all {{ $paymentMethod === 'transfer' ? 'border-primary-600 bg-primary-50 dark:bg-primary-950 text-primary-600' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300' }}"
                        >
                            <span class="text-xl">🏦</span> Transfer / EDC
                        </button>
                    </div>
                </div>

                {{-- Cash Specific Interface --}}
                @if($paymentMethod === 'cash')
                    <div class="space-y-3 p-3.5 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nominal Diterima</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-gray-500">Rp</span>
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.200ms="paidAmount"
                                    class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm font-bold text-gray-900 dark:text-gray-100"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        {{-- Quick Cash Buttons --}}
                        <div class="flex flex-wrap gap-1.5">
                            <button wire:click="setExactPayment" class="px-2.5 py-1 text-xs font-semibold rounded bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100">
                                Uang Pas
                            </button>
                            <button wire:click="setQuickCash(50000)" class="px-2.5 py-1 text-xs font-semibold rounded bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100">
                                50.000
                            </button>
                            <button wire:click="setQuickCash(100000)" class="px-2.5 py-1 text-xs font-semibold rounded bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100">
                                100.000
                            </button>
                            <button wire:click="setQuickCash(200000)" class="px-2.5 py-1 text-xs font-semibold rounded bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100">
                                200.000
                            </button>
                        </div>

                        {{-- Change Amount --}}
                        <div class="pt-2 flex justify-between items-center text-sm font-bold">
                            <span class="text-gray-700 dark:text-gray-300">Kembalian:</span>
                            <span class="text-base text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- QRIS Specific Interface (Interactive Mockup) --}}
                @if($paymentMethod === 'qris')
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl text-center space-y-2">
                        <div class="inline-block p-3 bg-white rounded-xl shadow-md border border-gray-200">
                            {{-- QR Mockup --}}
                            <div class="w-44 h-44 bg-gray-900 p-2 rounded-lg flex flex-col justify-between text-white relative">
                                <div class="flex justify-between">
                                    <div class="w-10 h-10 border-4 border-white"></div>
                                    <div class="w-10 h-10 border-4 border-white"></div>
                                </div>
                                <div class="text-[10px] font-mono tracking-widest text-center text-gray-300">
                                    QRIS STANDAR<br>
                                    NMID: ID102030405
                                </div>
                                <div class="flex justify-between items-end">
                                    <div class="w-10 h-10 border-4 border-white"></div>
                                    <div class="w-6 h-6 bg-white rounded-xs"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Scan QRIS menggunakan GoPay, OVO, Dana, BCA, Mandiri, dll.</p>
                        <p class="text-[11px] text-emerald-600 font-medium">✓ Simulasi: QRIS akan otomatis terverifikasi lunas saat tombol ditekan.</p>
                    </div>
                @endif

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Catatan Tambahan (Opsional)</label>
                    <input 
                        type="text" 
                        wire:model="notes" 
                        placeholder="Contoh: Meja 04 / Request bungkus"
                        class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                    />
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button 
                        type="button" 
                        wire:click="closeCheckoutModal"
                        class="w-1/3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="processCheckout"
                        class="w-2/3 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: STRUK PEMBAYARAN / RECEIPT --}}
    @if($showReceiptModal && $completedOrderData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800 space-y-4 animate-in zoom-in-95 duration-200">
                
                {{-- Thermal Paper Container --}}
                <div id="printable-receipt" class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 text-gray-800 dark:text-gray-200 font-mono text-xs space-y-3">
                    <div class="text-center space-y-1">
                        <h2 class="text-sm font-extrabold uppercase tracking-wider">POS & BOOKING STARTER</h2>
                        <p class="text-[10px] text-gray-500">Jl. Teknologi No. 88, Jakarta</p>
                        <p class="text-[10px] text-gray-500">Telp: 0812-3456-7890</p>
                    </div>

                    <div class="border-t border-dashed border-gray-300 dark:border-gray-700 pt-2 text-[10px] space-y-0.5">
                        <div class="flex justify-between">
                            <span>No: {{ $completedOrderData['invoice_number'] }}</span>
                            <span>{{ $completedOrderData['date'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kasir: {{ $completedOrderData['cashier'] }}</span>
                            <span>Cust: {{ $completedOrderData['customer'] }}</span>
                        </div>
                    </div>

                    {{-- Items List --}}
                    <div class="border-t border-dashed border-gray-300 dark:border-gray-700 pt-2 space-y-1.5">
                        @foreach($completedOrderData['items'] as $item)
                            <div>
                                <div class="font-bold text-[11px]">{{ $item['product_name'] }}</div>
                                <div class="flex justify-between text-[10px] text-gray-600 dark:text-gray-400">
                                    <span>{{ $item['quantity'] }} x Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</span>
                                    <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="border-t border-dashed border-gray-300 dark:border-gray-700 pt-2 text-[11px] space-y-1">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($completedOrderData['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        @if($completedOrderData['discount'] > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Diskon:</span>
                                <span>-Rp {{ number_format($completedOrderData['discount'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($completedOrderData['tax'] > 0)
                            <div class="flex justify-between">
                                <span>Pajak (PPN):</span>
                                <span>Rp {{ number_format($completedOrderData['tax'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-xs pt-1 border-t border-gray-300 dark:border-gray-700">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($completedOrderData['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[10px]">
                            <span>Metode ({{ $completedOrderData['payment_method'] }}):</span>
                            <span>Rp {{ number_format($completedOrderData['paid_amount'], 0, ',', '.') }}</span>
                        </div>
                        @if($completedOrderData['change_amount'] > 0)
                            <div class="flex justify-between text-[10px]">
                                <span>Kembalian:</span>
                                <span>Rp {{ number_format($completedOrderData['change_amount'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-dashed border-gray-300 dark:border-gray-700 pt-3 text-center text-[10px] space-y-1">
                        <p class="font-semibold">Terima Kasih Atas Kunjungan Anda!</p>
                        <p class="text-gray-400 text-[9px]">Sistem POS & Booking Starter Kit</p>
                    </div>
                </div>

                {{-- Modal Buttons --}}
                <div class="flex items-center gap-2 pt-1">
                    <button 
                        type="button" 
                        onclick="window.print()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Struk
                    </button>
                    <button 
                        type="button" 
                        wire:click="closeReceiptModal"
                        class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5"
                    >
                        Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
