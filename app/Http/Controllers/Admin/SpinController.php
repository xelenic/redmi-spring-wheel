<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use App\Models\Spin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SpinController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->string('group', 'list')->toString();

        $query = Spin::query()->with(['requestedPrize', 'awardedPrize'])->latest();
        $this->applyFilters($query, $request);

        $spins = $query->paginate(50)->withQueryString();

        $prizeOptions = Prize::query()
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get(['id', 'display_name']);

        $dailyStats = null;
        if ($group === 'day') {
            $statsQuery = Spin::query();
            $this->applyFilters($statsQuery, $request);

            // Cross-db friendly: date(created_at) works in SQLite/MySQL/Postgres.
            $dailyStats = $statsQuery
                ->selectRaw("date(created_at) as day")
                ->selectRaw("count(*) as total")
                ->selectRaw("sum(case when issued = 1 then 1 else 0 end) as issued_count")
                ->groupBy('day')
                ->orderBy('day', 'desc')
                ->get();
        }

        return view('admin.spins.index', compact('spins', 'group', 'dailyStats', 'prizeOptions'));
    }

    public function export(Request $request)
    {
        $query = Spin::query()->with(['requestedPrize', 'awardedPrize'])->latest();
        $this->applyFilters($query, $request);

        $filename = 'spin-history-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Date', 'Time', 'Name', 'Phone', 'Requested Prize', 'Awarded Prize', 'Result Label', 'Status', 'IP Address']);

            $query->chunk(500, function ($spins) use ($handle) {
                foreach ($spins as $spin) {
                    fputcsv($handle, [
                        $spin->created_at->format('Y-m-d'),
                        $spin->created_at->format('H:i:s'),
                        $spin->name ?? '',
                        $spin->phone ?? '',
                        $spin->requestedPrize->display_name ?? '',
                        $spin->awardedPrize->display_name ?? '',
                        $spin->result_label ?? '',
                        $spin->issued ? 'Issued' : 'Not Issued',
                        $spin->meta['ip'] ?? '',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Spin $spin)
    {
        $spin->load(['requestedPrize', 'awardedPrize']);

        return view('admin.spins.show', compact('spin'));
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        // Filter by awarded prize
        $awardedPrizeId = $request->input('awarded_prize_id');
        if ($awardedPrizeId !== null && $awardedPrizeId !== '' && $awardedPrizeId !== 'all') {
            if ($awardedPrizeId === 'none') {
                $query->whereNull('awarded_prize_id');
            } elseif (is_numeric($awardedPrizeId) && (int) $awardedPrizeId > 0) {
                $query->where('awarded_prize_id', (int) $awardedPrizeId);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by result label
        if ($request->filled('search')) {
            $query->where('result_label', 'like', '%' . $request->search . '%');
        }
    }
}





