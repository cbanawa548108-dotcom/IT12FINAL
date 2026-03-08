@extends('layouts.app')

@section('title', 'Password Change Logs')
@section('page_title', 'Password Change Logs')

@section('content')
<div class="container mx-auto px-4">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-green-700 flex items-center">
            <span class="material-icons mr-2">lock_reset</span>
            Password Change Logs
        </h1>
        <a href="{{ route('dashboard') }}"
           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
            <span class="material-icons mr-1" style="font-size:18px;">arrow_back</span>
            Back to Dashboard
        </a>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg shadow-md border border-blue-200">
            <div class="flex items-center">
                <div class="bg-blue-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size:32px;">lock_reset</span>
                </div>
                <div>
                    <div class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Changes</div>
                    <div class="text-3xl font-bold text-blue-800">{{ $logs->total() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg shadow-md border border-green-200">
            <div class="flex items-center">
                <div class="bg-green-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size:32px;">today</span>
                </div>
                <div>
                    <div class="text-xs text-green-600 font-semibold uppercase tracking-wide">Today</div>
                    <div class="text-3xl font-bold text-green-800">{{ $logs->where('created_at', '>=', now()->startOfDay())->count() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg shadow-md border border-purple-200">
            <div class="flex items-center">
                <div class="bg-purple-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size:32px;">date_range</span>
                </div>
                <div>
                    <div class="text-xs text-purple-600 font-semibold uppercase tracking-wide">This Month</div>
                    <div class="text-3xl font-bold text-purple-800">{{ $logs->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-green-700 to-green-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size:18px;">schedule</span>
                                Date & Time
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size:18px;">person</span>
                                User
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size:18px;">badge</span>
                                Role
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size:18px;">router</span>
                                IP Address
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size:18px;">devices</span>
                                Browser / OS
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        @php
                            $ua = $log->user_agent ?? '';
                            if (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg')) {
                                $browser = 'Chrome'; $browserColor = 'text-blue-500';
                            } elseif (str_contains($ua, 'Firefox')) {
                                $browser = 'Firefox'; $browserColor = 'text-orange-500';
                            } elseif (str_contains($ua, 'Edg')) {
                                $browser = 'Edge'; $browserColor = 'text-indigo-500';
                            } elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) {
                                $browser = 'Safari'; $browserColor = 'text-blue-400';
                            } else {
                                $browser = 'Unknown'; $browserColor = 'text-gray-400';
                            }
                            if (str_contains($ua, 'Windows')) { $os = 'Windows'; $osIcon = 'computer'; }
                            elseif (str_contains($ua, 'Macintosh')) { $os = 'MacOS'; $osIcon = 'laptop_mac'; }
                            elseif (str_contains($ua, 'iPhone')) { $os = 'iPhone'; $osIcon = 'smartphone'; }
                            elseif (str_contains($ua, 'Android')) { $os = 'Android'; $osIcon = 'smartphone'; }
                            elseif (str_contains($ua, 'Linux')) { $os = 'Linux'; $osIcon = 'computer'; }
                            else { $os = 'Unknown'; $osIcon = 'devices'; }
                        @endphp
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y') }}
                                </div>
                                <div class="text-gray-500 text-sm">
                                    {{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 bg-green-600 text-white rounded-full flex items-center justify-center font-bold mr-3">
                                            {{ substr($log->user->fname, 0, 1) }}{{ substr($log->user->lname, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $log->user->fname }} {{ $log->user->lname }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">Deleted User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                    @if($log->user->role === 'admin')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Admin</span>
                                    @elseif($log->user->role === 'manager')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Manager</span>
                                    @elseif($log->user->role === 'cashier')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Cashier</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded text-gray-800">
                                    {{ $log->ip_address ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center">
                                        <span class="material-icons {{ $browserColor }} mr-1" style="font-size:16px;">language</span>
                                        <span class="text-sm font-medium text-gray-800">{{ $browser }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="material-icons text-gray-400 mr-1" style="font-size:16px;">{{ $osIcon }}</span>
                                        <span class="text-xs text-gray-500">{{ $os }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="bg-green-100 p-6 rounded-full mb-4">
                                        <span class="material-icons text-green-600" style="font-size:64px;">lock</span>
                                    </div>
                                    <p class="text-xl font-bold text-gray-700 mb-2">No Password Changes Yet</p>
                                    <p class="text-sm text-gray-500">Password change history will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection