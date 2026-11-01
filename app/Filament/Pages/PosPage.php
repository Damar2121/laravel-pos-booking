<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class PosPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static string|UnitEnum|null $navigationGroup = 'Layanan & Penjualan';

    protected static ?string $navigationLabel = 'Mesin Kasir (POS)';

    protected static ?string $title = 'Mesin Kasir Interaktif (POS)';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.pos-page';

    // State properties
    public string $search = '';
    public string $selectedCategory = 'all';
    public array $cart = [];
    public ?int $customerId = null;
    public ?int $bookingId = null;
    public float $discount = 0;
    public float $taxPercent = 0;
    public string $paymentMethod = 'cash';
    public float $paidAmount = 0;
    public string $notes = '';

    // Modals
    public bool $showCheckoutModal = false;
    public bool $showReceiptModal = false;
    public ?array $completedOrderData = null;

    public function mount(): void
    {
        $this->cart = [];
    }

    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)->orderBy('name')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function getPendingBookingsProperty()
    {
        return Booking::with(['customer', 'product'])
            ->whereIn('status', ['confirmed', 'pending', 'in_progress'])
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    public function getProductsProperty()
    {
        $query = Product::where('is_active', true)
            ->with('category');

        if ($this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('sku', 'like', $term)
                    ->orWhere('barcode', 'like', $term);
            });
        }

        return $query->orderBy('name')->get();
    }

    public function selectCategory(string $categoryId): void
    {
        $this->selectedCategory = $categoryId;
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            Notification::make()->title('Produk tidak ditemukan')->danger()->send();
            return;
        }

        // Check stock for goods
        $currentQtyInCart = isset($this->cart[$productId]) ? $this->cart[$productId]['quantity'] : 0;
        if ($product->type === 'goods' && $product->stock <= $currentQtyInCart) {
            Notification::make()
                ->title('Stok Tidak Mencukupi')
                ->body("Sisa stok produk {$product->name} adalah {$product->stock}.")
                ->warning()
                ->send();
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 1,
                'subtotal' => (float) $product->price,
                'type' => $product->type,
                'image' => $product->image,
            ];
        }

        Notification::make()
            ->title("{$product->name} ditambahkan")
            ->success()
            ->duration(1500)
            ->send();
    }

    public function updateQuantity(int $productId, int $delta): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $newQty = $this->cart[$productId]['quantity'] + $delta;
        $product = Product::find($productId);

        if ($delta > 0 && $product && $product->type === 'goods' && $product->stock < $newQty) {
            Notification::make()
                ->title('Batas Stok Tercapai')
                ->body("Stok maksimum {$product->name} adalah {$product->stock}.")
                ->warning()
                ->send();
            return;
        }

        if ($newQty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $this->cart[$productId]['quantity'] = $newQty;
        $this->cart[$productId]['subtotal'] = $newQty * $this->cart[$productId]['price'];
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->discount = 0;
        $this->bookingId = null;
        Notification::make()->title('Keranjang dikosongkan')->info()->send();
    }

    public function loadBooking(int $bookingId): void
    {
        $booking = Booking::with(['customer', 'product'])->find($bookingId);
        if (!$booking) {
            return;
        }

        $this->bookingId = $booking->id;
        $this->customerId = $booking->customer_id;

        if ($booking->product_id) {
            $this->addToCart($booking->product_id);
        }

        Notification::make()
            ->title("Booking {$booking->booking_code} Dimuat")
            ->body("Pelanggan: {$booking->customer->name}")
            ->success()
            ->send();
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getTaxAmountProperty(): float
    {
        if ($this->taxPercent <= 0) {
            return 0;
        }
        $afterDiscount = max(0, $this->subtotal - $this->discount);
        return ($afterDiscount * $this->taxPercent) / 100;
    }

    public function getGrandTotalProperty(): float
    {
        $total = ($this->subtotal - $this->discount) + $this->taxAmount;
        return max(0, $total);
    }

    public function getChangeAmountProperty(): float
    {
        if ($this->paymentMethod === 'cash') {
            return max(0, $this->paidAmount - $this->grandTotal);
        }
        return 0;
    }

    public function setExactPayment(): void
    {
        $this->paidAmount = $this->grandTotal;
    }

    public function setQuickCash(float $amount): void
    {
        $this->paidAmount = $amount;
    }

    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Keranjang belanja masih kosong!')->warning()->send();
            return;
        }

        $this->paidAmount = $this->grandTotal;
        $this->showCheckoutModal = true;
    }

    public function closeCheckoutModal(): void
    {
        $this->showCheckoutModal = false;
    }

    public function processCheckout(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Keranjang belanja kosong!')->warning()->send();
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->paidAmount < $this->grandTotal) {
            Notification::make()
                ->title('Nominal Tunai Kurang')
                ->body('Nominal pembayaran tunai tidak boleh lebih kecil dari grand total.')
                ->danger()
                ->send();
            return;
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'customer_id' => $this->customerId,
                'user_id' => auth()->id() ?? 1,
                'booking_id' => $this->bookingId,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->taxAmount,
                'total' => $this->grandTotal,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'paid',
                'paid_amount' => $this->paymentMethod === 'cash' ? $this->paidAmount : $this->grandTotal,
                'change_amount' => $this->changeAmount,
                'notes' => $this->notes,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Deduct stock for goods
                $product = Product::find($item['id']);
                if ($product && $product->type === 'goods') {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // If customer exists, add loyalty points (1 point per Rp 10.000)
            if ($this->customerId) {
                $pointsEarned = (int) floor($this->grandTotal / 10000);
                if ($pointsEarned > 0) {
                    Customer::where('id', $this->customerId)->increment('points', $pointsEarned);
                }
            }

            // If linked to booking, complete booking
            if ($this->bookingId) {
                Booking::where('id', $this->bookingId)->update(['status' => 'completed']);
            }

            DB::commit();

            // Prepare receipt data
            $order->load(['customer', 'cashier', 'items']);
            $this->completedOrderData = [
                'invoice_number' => $order->invoice_number,
                'date' => $order->created_at->format('d/m/Y H:i'),
                'cashier' => $order->cashier ? $order->cashier->name : 'Kasir',
                'customer' => $order->customer ? $order->customer->name : 'Walk-in Customer',
                'items' => $order->items->toArray(),
                'subtotal' => $order->subtotal,
                'discount' => $order->discount,
                'tax' => $order->tax,
                'total' => $order->total,
                'payment_method' => strtoupper($order->payment_method),
                'paid_amount' => $order->paid_amount,
                'change_amount' => $order->change_amount,
                'notes' => $order->notes,
            ];

            // Reset cart
            $this->cart = [];
            $this->discount = 0;
            $this->taxPercent = 0;
            $this->bookingId = null;
            $this->notes = '';
            $this->showCheckoutModal = false;
            $this->showReceiptModal = true;

            Notification::make()
                ->title('Transaksi Berhasil!')
                ->body("Faktur {$order->invoice_number} berhasil dicatat.")
                ->success()
                ->send();

        } catch (\Throwable $e) {
            DB::rollBack();
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->completedOrderData = null;
    }
}
