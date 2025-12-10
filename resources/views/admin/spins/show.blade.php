@extends('admin.layout')

@section('title', 'Spin Details')
@section('page-title', 'Spin Details')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.spins.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Spin History
        </a>
    </div>

    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Spin Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spin->created_at->format('F d, Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @if($spin->issued)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Issued
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Not Issued
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Requested Prize</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spin->requestedPrize->display_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Awarded Prize</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spin->awardedPrize->display_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Result Label</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $spin->result_label }}</dd>
                </div>
            </dl>
        </div>

        @if($spin->meta)
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Metadata</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(isset($spin->meta['ip']))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $spin->meta['ip'] }}</dd>
                            </div>
                        @endif
                        @if(isset($spin->meta['user_agent']))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $spin->meta['user_agent'] }}</dd>
                            </div>
                        @endif
                        @if(isset($spin->meta['message']))
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Message</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $spin->meta['message'] }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        @endif

        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Full Metadata (JSON)</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <pre class="text-xs text-gray-700 overflow-x-auto">{{ json_encode($spin->meta, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection

