@extends('admin.layout')

@section('title', 'Spin History')
@section('page-title', 'Spin History')

@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('admin.spins.index') }}" class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Search by result...">
            </div>
            <div>
                <label for="awarded_prize_id" class="block text-gray-700 text-sm font-bold mb-2">Awarded Prize</label>
                <select id="awarded_prize_id" name="awarded_prize_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">All</option>
                    <option value="none" {{ request('awarded_prize_id') === 'none' ? 'selected' : '' }}>None</option>
                    @foreach(($prizeOptions ?? []) as $prize)
                        <option value="{{ $prize->id }}" {{ (string) request('awarded_prize_id') === (string) $prize->id ? 'selected' : '' }}>
                            {{ $prize->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-gray-700 text-sm font-bold mb-2">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="date_to" class="block text-gray-700 text-sm font-bold mb-2">To Date</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="group" class="block text-gray-700 text-sm font-bold mb-2">View</label>
                <select id="group" name="group" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="list" {{ request('group', 'list') === 'list' ? 'selected' : '' }}>List</option>
                    <option value="day" {{ request('group') === 'day' ? 'selected' : '' }}>Grouped by day</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.spins.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-times mr-2"></i>Clear
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 bg-gray-50 border-b">
        <p class="text-sm text-gray-600">Total: {{ $spins->total() }} spins</p>
    </div>

    @if(($group ?? request('group', 'list')) === 'day' && isset($dailyStats) && $dailyStats && $dailyStats->count())
        <div class="p-4 border-b bg-white">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Daily summary (filtered)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Not issued</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drill down</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dailyStats as $row)
                            @php
                                $day = $row->day;
                                $total = (int) ($row->total ?? 0);
                                $issuedCount = (int) ($row->issued_count ?? 0);
                                $notIssuedCount = max(0, $total - $issuedCount);
                                $query = request()->query();
                                $query['date_from'] = $day;
                                $query['date_to'] = $day;
                            @endphp
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ \Illuminate\Support\Carbon::parse($day)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($total) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($issuedCount) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($notIssuedCount) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm">
                                    <a class="text-blue-600 hover:text-blue-900" href="{{ route('admin.spins.index', $query) }}">
                                        View spins
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Prize</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Awarded Prize</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result Label</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php($isGrouped = ($group ?? request('group', 'list')) === 'day')

                @if($spins->count() === 0)
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No spins found</td>
                    </tr>
                @elseif(!$isGrouped)
                    @foreach($spins as $spin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $spin->created_at->format('M d, Y') }}<br>
                                <span class="text-xs">{{ $spin->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $spin->requestedPrize->display_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $spin->awardedPrize->display_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $spin->result_label }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($spin->issued)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Issued
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Not Issued
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $spin->meta['ip'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.spins.show', $spin) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    @php($groups = $spins->groupBy(fn ($spin) => $spin->created_at->toDateString()))
                    @foreach($groups as $date => $items)
                        <tr class="bg-gray-50">
                            <td colspan="7" class="px-6 py-3 text-sm font-semibold text-gray-800">
                                {{ \Illuminate\Support\Carbon::parse($date)->format('M d, Y') }}
                                <span class="ml-2 text-xs font-normal text-gray-500">({{ $items->count() }} spins on this page)</span>
                            </td>
                        </tr>
                        @foreach($items as $spin)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $spin->created_at->format('M d, Y') }}<br>
                                    <span class="text-xs">{{ $spin->created_at->format('H:i:s') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $spin->requestedPrize->display_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $spin->awardedPrize->display_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $spin->result_label }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($spin->issued)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Issued
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Not Issued
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $spin->meta['ip'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.spins.show', $spin) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    @if($spins->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $spins->firstItem() }} to {{ $spins->lastItem() }} of {{ $spins->total() }} results
                </div>
                <div class="flex space-x-2">
                    @if($spins->onFirstPage())
                        <span class="px-3 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $spins->previousPageUrl() }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Previous</a>
                    @endif
                    
                    @if($spins->hasMorePages())
                        <a href="{{ $spins->nextPageUrl() }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Next</a>
                    @else
                        <span class="px-3 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

