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
                'display_name' => 'Try Again',
                'stock_total' => null,
                'stock_remaining' => null,
                'image_path' => 'spin/03/better luck next time.png',
                'sort_order' => 1,
            ],
            [
                'key' => 'redmi-bud-5a',
                'display_name' => 'Redmi Buds 5A',
                'stock_total' => 50,
                'stock_remaining' => 50,
                'image_path' => 'spin_data/Gifts/Buds.png',
                'sort_order' => 2,
            ],
            [
                'key' => 'power-bank',
                'display_name' => 'Power Bank',
                'stock_total' => 50,
                'stock_remaining' => 50,
                'image_path' => 'spin_data/Gifts/PowerBank.png',
                'sort_order' => 3,
            ],
            [
                'key' => 'watch',
                'display_name' => 'Watch',
                'stock_total' => 50,
                'stock_remaining' => 50,
                'image_path' => 'spin_data/Gifts/Watch.png',
                'sort_order' => 4,
            ],
        ];

        foreach ($prizes as $data) {
            /** @var \App\Models\Prize $prize */
            $prize = Prize::query()->firstOrNew(['key' => $data['key']]);

            $prize->fill(Arr::except($data, ['stock_total', 'stock_remaining']));

            $prize->stock_total = $data['stock_total'];
            $prize->stock_remaining = $data['stock_remaining'];

            $prize->save();
        }
    }
}
