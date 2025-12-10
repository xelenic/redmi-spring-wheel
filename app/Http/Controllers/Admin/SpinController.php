<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spin;
use Illuminate\Http\Request;

class SpinController extends Controller
{
    public function index(Request $request)
    {
        $query = Spin::with(['requestedPrize', 'awardedPrize'])->latest();

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

        $spins = $query->paginate(50);

        return view('admin.spins.index', compact('spins'));
    }

    public function show(Spin $spin)
    {
        $spin->load(['requestedPrize', 'awardedPrize']);

        return view('admin.spins.show', compact('spin'));
    }
}

