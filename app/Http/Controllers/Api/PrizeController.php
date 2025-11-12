<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PrizeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $prizes = Prize::query()->get()->keyBy('key');
        $segmentOrder = config('prizes.segment_order', []);

        $segments = collect($segmentOrder)->map(function (string $key, int $index) use ($prizes): array {
            /** @var \App\Models\Prize|null $prize */
            $prize = $prizes->get($key);

            $displayName = $prize?->display_name ?? Str::of($key)->replace('-', ' ')->title();
            $image = $prize && $prize->image_path ? asset($prize->image_path) : null;

            return [
                'index' => $index,
                'key' => $prize?->key ?? $key,
                'label' => $displayName,
                'image' => $image,
                'remaining' => $prize?->stock_remaining,
                'total' => $prize?->stock_total,
            ];
        })->values();

        $summary = $this->formatSummary($prizes->values());

        return response()->json([
            'segments' => $segments,
            'summary' => $summary,
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    protected function formatSummary(Collection $prizes): Collection
    {
        return $prizes->map(function (Prize $prize): array {
            $image = $prize->image_path ? asset($prize->image_path) : null;

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
                'image' => $image,
            ];
        });
    }
}

