<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.spins.index', compact('spins', 'group', 'dailyStats'));
    }

    public function show(Spin $spin)
    {
        $spin->load(['requestedPrize', 'awardedPrize']);

        return view('admin.spins.show', compact('spin'));
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        // Filter by issued status
        if ($request->has('issued') && $request->issued !== '') {
            $query->where('issued', $request->boolean('issued'));
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





