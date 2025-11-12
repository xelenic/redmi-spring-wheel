<?php

use App\Models\Prize;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    $prizes = Prize::query()
        ->orderBy('sort_order')
        ->orderBy('display_name')
        ->get()
        ->keyBy('key');

    $segmentOrder = collect(config('prizes.segment_order', []));

    $initialSegments = $segmentOrder->map(function (string $key, int $index) use ($prizes) {
        /** @var \App\Models\Prize|null $prize */
        $prize = $prizes->get($key);

        $label = $prize?->display_name ?? Str::of($key)->replace('-', ' ')->title();
        $image = $prize && $prize->image_path ? asset($prize->image_path) : null;
        $remaining = $prize?->stock_remaining;
        $total = $prize?->stock_total;

        return [
            'index' => $index,
            'key' => $prize?->key ?? $key,
            'label' => $label,
            'image' => $image,
            'remaining' => $remaining,
            'total' => $total,
        ];
    })->values();

    $initialSummary = $prizes->values()->map(function (Prize $prize) {
        $issued = null;
        if ($prize->stock_total !== null && $prize->stock_remaining !== null) {
            $issued = max(0, $prize->stock_total - $prize->stock_remaining);
        }

        return [
            'key' => $prize->key,
            'label' => $prize->display_name,
            'total' => $prize->stock_total,
            'remaining' => $prize->stock_remaining,
            'issued' => $issued,
            'image' => $prize->image_path ? asset($prize->image_path) : null,
        ];
    });

    return view('welcome', [
        'initialSegments' => $initialSegments,
        'initialSummary' => $initialSummary,
    ]);
});
