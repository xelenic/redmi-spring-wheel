<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use App\Models\Spin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpinController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['nullable', 'string', 'exists:prizes,key'],
            'meta' => ['array'],
        ]);

        $meta = $validated['meta'] ?? [];

        $result = DB::transaction(function () use ($validated, $meta) {
            /** @var \App\Models\Prize $requestedPrize */
            $requestedPrize = $this->resolveRequestedPrize($validated['key'] ?? null);

            $awardedPrize = $requestedPrize;
            $issued = true;
            $message = null;

            if ($requestedPrize->isLimited()) {
                $remaining = $requestedPrize->stock_remaining ?? 0;

                if ($remaining > 0) {
                    $requestedPrize->stock_remaining = $remaining - 1;
                    $requestedPrize->save();
                } else {
                    $issued = false;
                    $awardedPrize = $this->fallbackPrize($requestedPrize) ?? $requestedPrize;
                    $message = sprintf('%s is out of stock.', $requestedPrize->display_name);
                }
            } else {
                // Unlimited prizes (like Try again) are not considered issued inventory.
                $issued = false;
            }

            if ($awardedPrize !== $requestedPrize && $awardedPrize->isLimited()) {
                $remaining = $awardedPrize->stock_remaining ?? 0;
                if ($remaining > 0) {
                    $awardedPrize->stock_remaining = $remaining - 1;
                    $awardedPrize->save();
                    $issued = true;
                } else {
                    $issued = false;
                }
            }

            $spin = Spin::create([
                'requested_prize_id' => $requestedPrize->id,
                'awarded_prize_id' => $awardedPrize->id ?? null,
                'issued' => $issued,
                'result_label' => $awardedPrize->display_name,
                'meta' => array_merge($meta, [
                    'ip' => request()->ip(),
                    'user_agent' => Str::limit((string) request()->userAgent(), 255),
                    'message' => $message,
                ]),
            ]);

            return [
                'spin' => $spin,
                'requested' => $requestedPrize,
                'awarded' => $awardedPrize,
                'issued' => $issued,
                'message' => $message,
            ];
        });

        $payload = [
            'issued' => $result['issued'],
            'message' => $result['message'],
            'requested' => $this->prizePayload($result['requested']),
            'result' => $this->prizePayload($result['awarded']),
            'summary' => $this->summaryPayload(),
        ];

        return response()->json($payload);
    }

    protected function prizePayload(?Prize $prize): ?array
    {
        if (!$prize) {
            return null;
        }

        return [
            'key' => $prize->key,
            'label' => $prize->display_name,
            'image' => $prize->image_path ? asset($prize->image_path) : null,
            'total' => $prize->stock_total,
            'remaining' => $prize->stock_remaining,
        ];
    }

    protected function summaryPayload()
    {
        $prizes = Prize::query()->get();

        return $prizes->map(function (Prize $prize) {
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
        })->values();
    }

    protected function fallbackPrize(Prize $requestedPrize): ?Prize
    {
        $fallbackKey = config('prizes.fallback_key');

        if (!$fallbackKey || $fallbackKey === $requestedPrize->key) {
            return $requestedPrize;
        }

        return Prize::query()->where('key', $fallbackKey)->lockForUpdate()->first();
    }

    protected function resolveRequestedPrize(?string $key): Prize
    {
        if ($key !== null) {
            return Prize::query()
                ->where('key', $key)
                ->lockForUpdate()
                ->firstOrFail();
        }

        return $this->drawRandomPrize();
    }

    protected function drawRandomPrize(): Prize
    {
        $prizes = Prize::query()->lockForUpdate()->get();

        if ($prizes->isEmpty()) {
            abort(422, 'No prizes available.');
        }

        $eligible = $prizes
            ->filter(fn (Prize $prize) => !$prize->isLimited() || ($prize->stock_remaining ?? 0) > 0)
            ->values();

        if ($eligible->isEmpty()) {
            return $prizes->first();
        }

        $weights = $eligible->mapWithKeys(fn (Prize $prize) => [
            $prize->key => $this->weightForPrize($prize),
        ]);

        $totalWeight = max(1, (int) $weights->sum());
        $pick = random_int(1, $totalWeight);

        foreach ($eligible as $prize) {
            $pick -= max(1, $weights->get($prize->key, 1));
            if ($pick <= 0) {
                return $prize;
            }
        }

        return $eligible->first();
    }

    protected function weightForPrize(Prize $prize): int
    {
        if ($prize->isLimited()) {
            return max(1, (int) ($prize->stock_remaining ?? 0));
        }

        return max(1, (int) config("prizes.unlimited_weights.{$prize->key}", 1));
    }
}

