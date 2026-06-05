<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'gender'   => 'Male',
            'address'  => 'Manila, Philippines',
        ]);

        // Create sample menu items
        $menuData = [
            ['name' => 'Chicken Adobo',      'category' => 'Main',      'price' => 120.00, 'description' => 'Classic Filipino chicken adobo'],
            ['name' => 'Pork Sinigang',       'category' => 'Main',      'price' => 140.00, 'description' => 'Sour tamarind broth with pork'],
            ['name' => 'Beef Caldereta',      'category' => 'Main',      'price' => 160.00, 'description' => 'Rich tomato-based beef stew'],
            ['name' => 'Pancit Canton',       'category' => 'Main',      'price' => 100.00, 'description' => 'Stir-fried noodles with vegetables'],
            ['name' => 'Lumpiang Shanghai',   'category' => 'Appetizer', 'price' => 60.00,  'description' => 'Crispy Filipino spring rolls'],
            ['name' => 'Tokwa\'t Baboy',      'category' => 'Appetizer', 'price' => 75.00,  'description' => 'Tofu and pork with vinegar dip'],
            ['name' => 'Steamed Rice',        'category' => 'Side',      'price' => 25.00,  'description' => 'Plain steamed white rice'],
            ['name' => 'Garlic Rice',         'category' => 'Side',      'price' => 35.00,  'description' => 'Fried rice with garlic'],
            ['name' => 'Halo-Halo',           'category' => 'Dessert',   'price' => 80.00,  'description' => 'Mixed Filipino shaved ice dessert'],
            ['name' => 'Leche Flan',          'category' => 'Dessert',   'price' => 55.00,  'description' => 'Classic Filipino caramel custard'],
            ['name' => 'Coke (Regular)',      'category' => 'Beverage',  'price' => 40.00,  'description' => '350ml can'],
            ['name' => 'Iced Mango Juice',    'category' => 'Beverage',  'price' => 65.00,  'description' => 'Fresh blended mango with ice'],
            ['name' => 'Mineral Water',       'category' => 'Beverage',  'price' => 25.00,  'description' => '500ml bottle'],
        ];

        $menuItems = [];
        foreach ($menuData as $data) {
            $menuItems[] = MenuItem::create(array_merge($data, ['user_id' => $admin->id, 'available' => true]));
        }

        // Create sample orders
        $sampleOrders = [
            ['customer' => 'Maria Santos',  'table' => 'Table 1', 'status' => 'served',    'days_ago' => 6],
            ['customer' => 'Jose Reyes',    'table' => 'Table 3', 'status' => 'served',    'days_ago' => 5],
            ['customer' => 'Ana Cruz',      'table' => 'Table 2', 'status' => 'served',    'days_ago' => 4],
            ['customer' => 'Pedro Lim',     'table' => 'Table 5', 'status' => 'served',    'days_ago' => 3],
            ['customer' => 'Rosa Bautista', 'table' => 'Table 4', 'status' => 'served',    'days_ago' => 2],
            ['customer' => 'Luis Garcia',   'table' => 'Table 1', 'status' => 'served',    'days_ago' => 1],
            ['customer' => 'Carla Ramos',   'table' => 'Table 2', 'status' => 'preparing', 'days_ago' => 0],
            ['customer' => 'Ben Flores',    'table' => 'Table 6', 'status' => 'pending',   'days_ago' => 0],
        ];

        $orderItemGroups = [
            [0, 6, 10],   // Chicken Adobo, Rice, Coke
            [1, 7, 11],   // Sinigang, Garlic Rice, Mango Juice
            [2, 6, 9],    // Caldereta, Rice, Leche Flan
            [3, 4, 12],   // Pancit, Lumpiang, Water
            [0, 1, 6, 8], // Adobo, Sinigang, Rice, Halo-Halo
            [2, 5, 7, 10],// Caldereta, Tokwa, Garlic Rice, Coke
            [0, 6, 11],   // Adobo, Rice, Mango Juice
            [3, 7, 12],   // Pancit, Garlic Rice, Water
        ];

        foreach ($sampleOrders as $i => $orderData) {
            $total = 0;
            $order = Order::create([
                'user_id'       => $admin->id,
                'customer_name' => $orderData['customer'],
                'table_number'  => $orderData['table'],
                'status'        => $orderData['status'],
                'total_amount'  => 0,
                'created_at'    => now()->subDays($orderData['days_ago'])->subHours(rand(0, 8)),
                'updated_at'    => now()->subDays($orderData['days_ago']),
            ]);

            foreach ($orderItemGroups[$i] as $idx) {
                $menuItem = $menuItems[$idx];
                $qty      = rand(1, 3);
                $subtotal = $menuItem->price * $qty;
                $total   += $subtotal;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $qty,
                    'unit_price'   => $menuItem->price,
                    'subtotal'     => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $total]);
        }
    }
}
