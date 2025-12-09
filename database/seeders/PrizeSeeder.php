<?php

namespace Database\Seeders;

use App\Models\Prize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prizes = [
            [
                'key' => 'try-again',
                'display_name' => 'Try again',
                'stock_total' => null,
                'stock_remaining' => null,
                'image_path' => 'spin/03/better luck next time.png',
                'sort_order' => 1,
            ],
            [
                'key' => 'chocolate-lava-cake',
                'display_name' => 'Chocolate Lava Cake',
                'stock_total' => 100,
                'stock_remaining' => 100,
                'image_path' => 'spin/gifts/Chocolate Lava Cake.png',
                'sort_order' => 2,
            ],
            [
                'key' => 'large-extra-bbq-pizza',
                'display_name' => 'Large Extra BBQ Pizza',
                'stock_total' => 30,
                'stock_remaining' => 30,
                'image_path' => 'spin/gifts/Large Texas BBQ Pizza.png',
                'sort_order' => 3,
            ],
            [
                'key' => 'garlic-bread',
                'display_name' => 'Garlic Bread',
                'stock_total' => 50,
                'stock_remaining' => 50,
                'image_path' => 'spin/gifts/Theriyaki Stuffed Garlic Bread.png',
                'sort_order' => 4,
            ],
            [
                'key' => 'regular-peperoni-pizza',
                'display_name' => 'Regular Peperoni Pizza',
                'stock_total' => 20,
                'stock_remaining' => 20,
                'image_path' => 'spin/gifts/Regular Pepperoni Pizza.png',
                'sort_order' => 5,
            ],
        ];

        foreach ($prizes as $data) {
            /** @var \App\Models\Prize $prize */
            $prize = Prize::query()->firstOrNew(['key' => $data['key']]);

            $prize->fill(Arr::except($data, ['stock_total', 'stock_remaining']));

            if ($data['stock_total'] !== null) {
                $prize->stock_total = $data['stock_total'];
            }

            if ($data['stock_remaining'] !== null) {
                $prize->stock_remaining = $data['stock_remaining'];
            }

            // Ensure unlimited prizes stay null.
            if ($data['stock_total'] === null) {
                $prize->stock_total = null;
            }

            if ($data['stock_remaining'] === null) {
                $prize->stock_remaining = null;
            }

            $prize->save();
        }
    }
}

