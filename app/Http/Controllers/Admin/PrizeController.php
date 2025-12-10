<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrizeController extends Controller
{
    public function index()
    {
        $prizes = Prize::orderBy('sort_order')->get();

        return view('admin.prizes.index', compact('prizes'));
    }

    public function create()
    {
        return view('admin.prizes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'unique:prizes,key'],
            'display_name' => ['required', 'string', 'max:255'],
            'stock_total' => ['nullable', 'integer', 'min:0'],
            'stock_remaining' => ['nullable', 'integer', 'min:0'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Prize::create($validated);

        return redirect()->route('admin.prizes.index')
            ->with('success', 'Prize created successfully.');
    }

    public function edit(Prize $prize)
    {
        return view('admin.prizes.edit', compact('prize'));
    }

    public function update(Request $request, Prize $prize)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'unique:prizes,key,' . $prize->id],
            'display_name' => ['required', 'string', 'max:255'],
            'stock_total' => ['nullable', 'integer', 'min:0'],
            'stock_remaining' => ['nullable', 'integer', 'min:0'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $prize->update($validated);

        return redirect()->route('admin.prizes.index')
            ->with('success', 'Prize updated successfully.');
    }

    public function destroy(Prize $prize)
    {
        $prize->delete();

        return redirect()->route('admin.prizes.index')
            ->with('success', 'Prize deleted successfully.');
    }

    public function updateStock(Request $request, Prize $prize)
    {
        $validated = $request->validate([
            'stock_remaining' => ['required', 'integer', 'min:0'],
        ]);

        $prize->update([
            'stock_remaining' => $validated['stock_remaining'],
        ]);

        return redirect()->route('admin.prizes.index')
            ->with('success', 'Stock updated successfully.');
    }
}

