<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $cashierRole = Role::firstOrCreate(['name' => 'Kasir']);

        // 2. Setup Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$adminRole]);

        $cashier = User::firstOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir Utama',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $cashier->syncRoles([$cashierRole]);

        // 3. Setup Categories
        $categories = [
            [
                'name' => 'Coffee & Drinks',
                'slug' => 'coffee-and-drinks',
                'icon' => 'heroicon-o-cup-soda',
                'description' => 'Minuman segar, kopi, teh, dan aneka beverage spesial.',
                'is_active' => true,
            ],
            [
                'name' => 'Food & Bakery',
                'slug' => 'food-and-bakery',
                'icon' => 'heroicon-o-cake',
                'description' => 'Makanan berat, pastry, dan camilan nikmat.',
                'is_active' => true,
            ],
            [
                'name' => 'Barbershop Services',
                'slug' => 'barbershop-services',
                'icon' => 'heroicon-o-scissors',
                'description' => 'Layanan potong rambut, styling, dan perawatan rambut profesional.',
                'is_active' => true,
            ],
            [
                'name' => 'Spa & Massage',
                'slug' => 'spa-and-massage',
                'icon' => 'heroicon-o-sparkles',
                'description' => 'Layanan relaksasi tubuh dan perawatan wajah eksklusif.',
                'is_active' => true,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 4. Setup Products (Goods & Services)
        $products = [
            // Goods
            [
                'category_id' => $createdCategories['coffee-and-drinks']->id,
                'name' => 'Caramel Macchiato',
                'slug' => 'caramel-macchiato',
                'sku' => 'DRK-001',
                'barcode' => '899001001',
                'price' => 32000,
                'cost_price' => 15000,
                'stock' => 50,
                'type' => 'goods',
                'image' => null,
                'description' => 'Espresso dengan sirup vanila, susu kukus lembut, dan lelehan karamel.',
                'is_active' => true,
            ],
            [
                'category_id' => $createdCategories['coffee-and-drinks']->id,
                'name' => 'Iced Matcha Latte',
                'slug' => 'iced-matcha-latte',
                'sku' => 'DRK-002',
                'barcode' => '899001002',
                'price' => 28000,
                'cost_price' => 13000,
                'stock' => 45,
                'type' => 'goods',
                'image' => null,
                'description' => 'Pure green tea matcha Uji Jepang dengan susu creamy dingin.',
                'is_active' => true,
            ],
            [
                'category_id' => $createdCategories['coffee-and-drinks']->id,
                'name' => 'Americano Special Roast',
                'slug' => 'americano-special-roast',
                'sku' => 'DRK-003',
                'barcode' => '899001003',
                'price' => 22000,
                'cost_price' => 8000,
                'stock' => 80,
                'type' => 'goods',
                'image' => null,
                'description' => 'Espresso double shot dicampur air mineral segar, aroma pekat seimbang.',
                'is_active' => true,
            ],
            [
                'category_id' => $createdCategories['food-and-bakery']->id,
                'name' => 'Butter Croissant Flaky',
                'slug' => 'butter-croissant-flaky',
                'sku' => 'FOD-001',
                'barcode' => '899002001',
                'price' => 25000,
                'cost_price' => 12000,
                'stock' => 30,
                'type' => 'goods',
                'image' => null,
                'description' => 'Pastry khas Prancis renyah di luar, lembut berlapis di dalam.',
                'is_active' => true,
            ],
            [
                'category_id' => $createdCategories['food-and-bakery']->id,
                'name' => 'Signature Beef Burger',
                'slug' => 'signature-beef-burger',
                'sku' => 'FOD-002',
                'barcode' => '899002002',
                'price' => 45000,
                'cost_price' => 25000,
                'stock' => 20,
                'type' => 'goods',
                'image' => null,
                'description' => 'Daging sapi panggang juicy dengan keju cheddar leleh dan saus spesial.',
                'is_active' => true,
            ],

            // Services (Booking-friendly)
            [
                'category_id' => $createdCategories['barbershop-services']->id,
                'name' => 'Gentleman Haircut & Styling',
                'slug' => 'gentleman-haircut-and-styling',
                'sku' => 'SRV-001',
                'barcode' => '899003001',
                'price' => 65000,
                'cost_price' => 10000,
                'stock' => 999,
                'type' => 'service',
                'image' => null,
                'description' => 'Potong rambut presisi, cuci rambut wangi, pijat kepala, dan finishing pomade.',
                'is_active' => true,
            ],
            [
                'category_id' => $createdCategories['barbershop-services']->id,
                'name' => 'Beard Grooming & Hot Towel',
                'slug' => 'beard-grooming-and-hot-towel',
                'sku' => 'SRV-002',
                'barcode' => '899003002',
                'price' => 40000,
                'cost_price' => 5000,
                'stock' => 999,
                'type' => 'service',
                'image' => null,
                'description' => 'Cukur rapi dengan relaksasi handuk hangat dan aftershave soothing.',
                'is_active' => true,
            ],
            [
                'category_id' => $createdCategories['spa-and-massage']->id,
                'name' => 'Traditional Full Body Massage (60 Min)',
                'slug' => 'traditional-full-body-massage-60',
                'sku' => 'SRV-003',
                'barcode' => '899004001',
                'price' => 120000,
                'cost_price' => 30000,
                'stock' => 999,
                'type' => 'service',
                'image' => null,
                'description' => 'Pijat relaksasi seluruh tubuh dengan minyak aromaterapi esensial.',
                'is_active' => true,
            ],
            [
                'category_id' => $createdCategories['spa-and-massage']->id,
                'name' => 'Hydrating Facial Treatment (45 Min)',
                'slug' => 'hydrating-facial-treatment-45',
                'sku' => 'SRV-004',
                'barcode' => '899004002',
                'price' => 95000,
                'cost_price' => 25000,
                'stock' => 999,
                'type' => 'service',
                'image' => null,
                'description' => 'Pembersihan mendalam wajah, eksfoliasi lembut, dan masker hidrasi.',
                'is_active' => true,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $prod) {
            $createdProducts[$prod['sku']] = Product::firstOrCreate(['sku' => $prod['sku']], $prod);
        }

        // 5. Setup Customers
        $customers = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'budi.santoso@gmail.com',
                'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
                'points' => 150,
                'notes' => 'Pelanggan setia kopi susu & haircut bulanan.',
            ],
            [
                'name' => 'Siti Rahma',
                'phone' => '081987654321',
                'email' => 'siti.rahma@gmail.com',
                'address' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'points' => 80,
                'notes' => 'Suka facial treatment akhir pekan.',
            ],
            [
                'name' => 'Andi Pratama',
                'phone' => '085711223344',
                'email' => 'andi.pratama@yahoo.com',
                'address' => 'Jl. Diponegoro No. 8, Bandung',
                'points' => 30,
                'notes' => 'Regular customer.',
            ],
            [
                'name' => 'Dewi Lestari',
                'phone' => '087855667788',
                'email' => 'dewi.lestari@gmail.com',
                'address' => 'Jl. Malioboro No. 22, Yogyakarta',
                'points' => 200,
                'notes' => 'VIP Member Spa & Massage.',
            ],
        ];

        $createdCustomers = [];
        foreach ($customers as $cust) {
            $createdCustomers[] = Customer::firstOrCreate(['phone' => $cust['phone']], $cust);
        }

        // 6. Setup Dummy Bookings
        $bookingToday = Booking::firstOrCreate(
            ['booking_code' => 'BKG-'.date('Ymd').'-001'],
            [
                'customer_id' => $createdCustomers[0]->id,
                'product_id' => $createdProducts['SRV-001']->id,
                'user_id' => $cashier->id,
                'booking_date' => now()->toDateString(),
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'total_price' => 65000,
                'status' => 'confirmed',
                'notes' => 'Request barber senior.',
            ]
        );

        $bookingTomorrow = Booking::firstOrCreate(
            ['booking_code' => 'BKG-'.date('Ymd', strtotime('+1 day')).'-002'],
            [
                'customer_id' => $createdCustomers[1]->id,
                'product_id' => $createdProducts['SRV-004']->id,
                'user_id' => $cashier->id,
                'booking_date' => now()->addDay()->toDateString(),
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'total_price' => 95000,
                'status' => 'pending',
                'notes' => 'Treatment wajah sebelum acara kantor.',
            ]
        );

        $bookingNextWeek = Booking::firstOrCreate(
            ['booking_code' => 'BKG-'.date('Ymd', strtotime('+3 days')).'-003'],
            [
                'customer_id' => $createdCustomers[3]->id,
                'product_id' => $createdProducts['SRV-003']->id,
                'user_id' => $cashier->id,
                'booking_date' => now()->addDays(3)->toDateString(),
                'start_time' => '16:00:00',
                'end_time' => '17:00:00',
                'total_price' => 120000,
                'status' => 'confirmed',
                'notes' => 'Aromaterapi lavender.',
            ]
        );

        // 7. Setup Dummy Orders (Sample Sales Transactions)
        $order1 = Order::firstOrCreate(
            ['invoice_number' => 'INV-'.date('Ymd').'-0001'],
            [
                'customer_id' => $createdCustomers[0]->id,
                'user_id' => $cashier->id,
                'subtotal' => 57000,
                'discount' => 0,
                'tax' => 0,
                'total' => 57000,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'paid_amount' => 57000,
                'change_amount' => 0,
                'notes' => 'Pembayaran via QRIS Berhasil.',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'product_id' => $createdProducts['DRK-001']->id],
            [
                'product_name' => 'Caramel Macchiato',
                'unit_price' => 32000,
                'quantity' => 1,
                'subtotal' => 32000,
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'product_id' => $createdProducts['FOD-001']->id],
            [
                'product_name' => 'Butter Croissant Flaky',
                'unit_price' => 25000,
                'quantity' => 1,
                'subtotal' => 25000,
            ]
        );

        $order2 = Order::firstOrCreate(
            ['invoice_number' => 'INV-'.date('Ymd').'-0002'],
            [
                'customer_id' => $createdCustomers[2]->id,
                'user_id' => $admin->id,
                'subtotal' => 73000,
                'discount' => 3000,
                'tax' => 0,
                'total' => 70000,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'paid_amount' => 100000,
                'change_amount' => 30000,
                'notes' => 'Pembayaran Tunai.',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'product_id' => $createdProducts['DRK-002']->id],
            [
                'product_name' => 'Iced Matcha Latte',
                'unit_price' => 28000,
                'quantity' => 1,
                'subtotal' => 28000,
            ]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'product_id' => $createdProducts['FOD-002']->id],
            [
                'product_name' => 'Signature Beef Burger',
                'unit_price' => 45000,
                'quantity' => 1,
                'subtotal' => 45000,
            ]
        );
    }
}
