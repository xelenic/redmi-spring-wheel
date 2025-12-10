@extends('admin.layout')

@section('title', 'Prizes Management')
@section('page-title', 'Prizes Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">All Prizes</h2>
    <a href="{{ route('admin.prizes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        <i class="fas fa-plus mr-2"></i>Add New Prize
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Display Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($prizes as $prize)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($prize->image_path)
                            <img src="{{ asset($prize->image_path) }}" alt="{{ $prize->display_name }}" class="h-12 w-12 object-cover rounded">
                        @else
                            <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $prize->key }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $prize->display_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($prize->stock_total !== null)
                            <span class="font-semibold">{{ $prize->stock_remaining ?? 0 }}</span> / {{ $prize->stock_total }}
                        @else
                            <span class="text-gray-400">Unlimited</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $prize->sort_order }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.prizes.edit', $prize) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.prizes.destroy', $prize) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this prize?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No prizes found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

