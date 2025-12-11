<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use App\Models\Spin;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSpins = Spin::count();
        $issuedPrizes = Spin::where('issued', true)->count();
        $totalPrizes = Prize::count();
        $limitedPrizes = Prize::whereNotNull('stock_total')->count();

        $recentSpins = Spin::with(['requestedPrize', 'awardedPrize'])
            ->latest()
            ->limit(10)
            ->get();

        $prizeStats = Prize::withCount(['spinsAwarded as awarded_count'])
            ->get()
            ->map(function ($prize) {
                $issued = null;
                if ($prize->stock_total !== null && $prize->stock_remaining !== null) {
                    $issued = max(0, $prize->stock_total - $prize->stock_remaining);
                }

                return [
                    'name' => $prize->display_name,
                    'total' => $prize->stock_total,
                    'remaining' => $prize->stock_remaining,
                    'issued' => $issued,
                    'awarded_count' => $prize->awarded_count,
                ];
            });

        $todaySpins = Spin::whereDate('created_at', today())->count();
        $thisWeekSpins = Spin::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthSpins = Spin::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.dashboard', compact(
            'totalSpins',
            'issuedPrizes',
            'totalPrizes',
            'limitedPrizes',
            'recentSpins',
            'prizeStats',
            'todaySpins',
            'thisWeekSpins',
            'thisMonthSpins'
        ));
    }
}


