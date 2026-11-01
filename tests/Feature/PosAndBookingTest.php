<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosAndBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category_product_and_booking(): void
    {
        $category = Category::create([
            'name' => 'Layanan Salon',
            'slug' => 'layanan-salon',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Potong Rambut Stylish',
            'slug' => 'potong-rambut-stylish',
            'sku' => 'SRV-TEST',
            'price' => 50000,
            'cost_price' => 10000,
            'stock' => 999,
            'type' => 'service',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '08123456789',
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'booking_date' => now()->toDateString(),
            'start_time' => '13:00',
            'total_price' => 50000,
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);

        $user = User::factory()->create();

        $order = Order::create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'subtotal' => 50000,
            'discount' => 0,
            'tax' => 0,
            'total' => 50000,
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'paid_amount' => 50000,
            'change_amount' => 0,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_method' => 'qris',
            'payment_status' => 'paid',
        ]);
    }
}
