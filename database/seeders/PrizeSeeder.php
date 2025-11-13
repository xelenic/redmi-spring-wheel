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
                'key' => 'water-bottle',
                'display_name' => 'Water bottle',
                'stock_total' => 20,
                'stock_remaining' => 20,
                'image_path' => 'spin/gifts/Water Bottle.png',
                'sort_order' => 1,
            ],
            [
                'key' => 'ice-cream',
                'display_name' => 'Ice cream',
                'stock_total' => 100,
                'stock_remaining' => 100,
                'image_path' => 'spin/gifts/ice_cream.png',
                'sort_order' => 2,
            ],
            [
                'key' => 'try-again',
                'display_name' => 'Try again',
                'stock_total' => 300,
                'stock_remaining' => 300,
                'image_path' => 'spin/03/better luck next time.png',
                'sort_order' => 3,
            ],
            [
                'key' => 't-shirt',
                'display_name' => 'T shirt',
                'stock_total' => 30,
                'stock_remaining' => 30,
                'image_path' => 'spin/gifts/T-SHIRT.png',
                'sort_order' => 4,
            ],
            [
                'key' => 'mug',
                'display_name' => 'Mug',
                'stock_total' => 50,
                'stock_remaining' => 50,
                'image_path' => 'spin/gifts/MUG.png',
                'sort_order' => 5,
            ],
            [
                'key' => 'umbrella',
                'display_name' => 'Umbrella',
                'stock_total' => 20,
                'stock_remaining' => 20,
                'image_path' => 'spin/gifts/umbrella.png',
                'sort_order' => 6,
            ],
            [
                'key' => 'cap',
                'display_name' => 'Cap',
                'stock_total' => 50,
                'stock_remaining' => 50,
                'image_path' => 'spin/gifts/CAP.png',
                'sort_order' => 7,
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

