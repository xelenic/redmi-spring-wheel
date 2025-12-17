@extends('admin.layout')

@section('title', 'Create Prize')
@section('page-title', 'Create New Prize')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.prizes.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="key" class="block text-gray-700 text-sm font-bold mb-2">Key (unique identifier)</label>
            <input type="text" id="key" name="key" value="{{ old('key') }}" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="e.g., chocolate-lava-cake">
            <p class="text-gray-500 text-xs mt-1">Use lowercase letters and hyphens only</p>
        </div>

        <div class="mb-4">
            <label for="display_name" class="block text-gray-700 text-sm font-bold mb-2">Display Name</label>
            <input type="text" id="display_name" name="display_name" value="{{ old('display_name') }}" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="e.g., Chocolate Lava Cake">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="stock_total" class="block text-gray-700 text-sm font-bold mb-2">Total Stock</label>
                <input type="number" id="stock_total" name="stock_total" value="{{ old('stock_total') }}" min="0"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Leave empty for unlimited">
            </div>
            <div>
                <label for="stock_remaining" class="block text-gray-700 text-sm font-bold mb-2">Remaining Stock</label>
                <input type="number" id="stock_remaining" name="stock_remaining" value="{{ old('stock_remaining') }}" min="0"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Leave empty for unlimited">
            </div>
        </div>

        <div class="mb-4">
            <label for="image_path" class="block text-gray-700 text-sm font-bold mb-2">Image Path</label>
            <input type="text" id="image_path" name="image_path" value="{{ old('image_path') }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="e.g., spin/gifts/Chocolate Lava Cake.png">
        </div>

        <div class="mb-4">
            <label for="sort_order" class="block text-gray-700 text-sm font-bold mb-2">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.prizes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Prize
            </button>
        </div>
    </form>
</div>
@endsection





