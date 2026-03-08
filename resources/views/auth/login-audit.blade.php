@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-green-700">Login Audit Log</h1>
        <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
            <span class="material-icons align-middle mr-1" style="font-size: 18px;">arrow_back</span>
            Back to Dashboard
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg shadow-md border border-green-200 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-green-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size: 32px;">check_circle</span>
                </div>
                <div>
                    <div class="text-xs text-green-600 font-semibold uppercase tracking-wide">Successful Logins</div>
                    <div class="text-3xl font-bold text-green-800">{{ $auditLogs->where('status', 'success')->count() }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg shadow-md border border-red-200 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-red-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size: 32px;">cancel</span>
                </div>
                <div>
                    <div class="text-xs text-red-600 font-semibold uppercase tracking-wide">Failed Attempts</div>
                    <div class="text-3xl font-bold text-red-800">{{ $auditLogs->where('status', 'failed')->count() }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg shadow-md border border-orange-200 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-orange-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size: 32px;">lock</span>
                </div>
                <div>
                    <div class="text-xs text-orange-600 font-semibold uppercase tracking-wide">Account Lockouts</div>
                    <div class="text-3xl font-bold text-orange-800">{{ $auditLogs->where('status', 'locked')->count() }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg shadow-md border border-purple-200 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-purple-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size: 32px;">history</span>
                </div>
                <div>
                    <div class="text-xs text-purple-600 font-semibold uppercase tracking-wide">Total Records</div>
                    <div class="text-3xl font-bold text-purple-800">{{ $auditLogs->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-green-700 to-green-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">schedule</span>
                                Date & Time
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">email</span>
                                Email
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">person</span>
                                User / Role
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">router</span>
                                IP Address
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">info</span>
                                Status
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">error</span>
                                Failure Reason
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">format_list_numbered</span>
                                Attempts
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">devices</span>
                                Browser / Device
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex items-center">
                                <span class="material-icons mr-2" style="font-size: 18px;">lock_clock</span>
                                Locked Until
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($auditLogs as $log)
                        @php
                            $userAccount = $log->user ?? \App\Models\User::where('email', $log->email)->first();

                            $emailParts  = explode('@', $log->email);
                            $emailName   = $emailParts[0] ?? '';
                            $emailDomain = $emailParts[1] ?? '';
                            $maskedEmail = substr($emailName, 0, 2) . str_repeat('*', max(strlen($emailName) - 2, 3)) . '@' . $emailDomain;

                            $ua = $log->user_agent ?? '';
                            if (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg')) {
                                $browser = 'Chrome'; $browserIcon = 'language'; $browserColor = 'text-blue-500';
                            } elseif (str_contains($ua, 'Firefox')) {
                                $browser = 'Firefox'; $browserIcon = 'language'; $browserColor = 'text-orange-500';
                            } elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) {
                                $browser = 'Safari'; $browserIcon = 'language'; $browserColor = 'text-blue-400';
                            } elseif (str_contains($ua, 'Edg')) {
                                $browser = 'Edge'; $browserIcon = 'language'; $browserColor = 'text-indigo-500';
                            } else {
                                $browser = 'Unknown Browser'; $browserIcon = 'language'; $browserColor = 'text-gray-400';
                            }

                            if (str_contains($ua, 'Windows')) {
                                $os = 'Windows'; $osIcon = 'computer';
                            } elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) {
                                $os = 'MacOS'; $osIcon = 'laptop_mac';
                            } elseif (str_contains($ua, 'iPhone')) {
                                $os = 'iPhone'; $osIcon = 'smartphone';
                            } elseif (str_contains($ua, 'Android')) {
                                $os = 'Android'; $osIcon = 'smartphone';
                            } elseif (str_contains($ua, 'Linux')) {
                                $os = 'Linux'; $osIcon = 'computer';
                            } else {
                                $os = 'Unknown OS'; $osIcon = 'devices';
                            }
                        @endphp
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y') }}
                                    </div>
                                    <div class="text-gray-600">
                                        {{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('h:i A') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $maskedEmail }}</div>
                                <div class="text-xs text-gray-400 flex items-center mt-0.5">
                                    <span class="material-icons mr-1" style="font-size: 12px;">lock</span>
                                    masked for privacy
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($userAccount)
                                    <div class="flex items-center">
                                        @if($userAccount->role == 'admin')
                                            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-full flex items-center justify-center mr-3 font-bold shadow">
                                                {{ substr($userAccount->fname ?? 'A', 0, 1) }}{{ substr($userAccount->lname ?? 'D', 0, 1) }}
                                            </div>
                                        @elseif($userAccount->role == 'manager')
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center mr-3 font-bold shadow">
                                                {{ substr($userAccount->fname ?? 'M', 0, 1) }}{{ substr($userAccount->lname ?? 'N', 0, 1) }}
                                            </div>
                                        @elseif($userAccount->role == 'cashier')
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center mr-3 font-bold shadow">
                                                {{ substr($userAccount->fname ?? 'C', 0, 1) }}{{ substr($userAccount->lname ?? 'S', 0, 1) }}
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 text-white rounded-full flex items-center justify-center mr-3 font-bold shadow">
                                                {{ substr($userAccount->fname ?? 'U', 0, 1) }}{{ substr($userAccount->lname ?? 'N', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $userAccount->fname }} {{ $userAccount->lname }}</div>
                                            <div class="text-xs text-gray-600">
                                                @if($userAccount->role == 'admin')
                                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full font-medium">
                                                        <span class="material-icons align-middle" style="font-size: 12px;">admin_panel_settings</span> Admin
                                                    </span>
                                                @elseif($userAccount->role == 'manager')
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full font-medium">
                                                        <span class="material-icons align-middle" style="font-size: 12px;">supervisor_account</span> Manager
                                                    </span>
                                                @elseif($userAccount->role == 'cashier')
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full font-medium">
                                                        <span class="material-icons align-middle" style="font-size: 12px;">point_of_sale</span> Cashier
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full font-medium">
                                                        {{ ucfirst($userAccount->role) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 text-white rounded-full flex items-center justify-center mr-3 font-bold shadow">
                                            <span class="material-icons" style="font-size: 20px;">help_outline</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-700">Unregistered</div>
                                            <div class="text-xs text-gray-500">
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full font-medium">
                                                    <span class="material-icons align-middle" style="font-size: 12px;">person_off</span> Invalid Email
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- ── IP Address column ── --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <span class="material-icons text-green-600 mr-1" style="font-size: 16px;">router</span>
                                    <span class="font-mono text-sm font-semibold text-gray-800 bg-green-50 border border-green-200 px-2 py-0.5 rounded">
                                        {{ $log->ip_address ?? '-' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @if($log->status == 'success')
                                    <span class="px-3 py-1.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full flex items-center w-fit shadow-sm">
                                        <span class="material-icons text-green-600 mr-1" style="font-size: 16px;">check_circle</span> Success
                                    </span>
                                @elseif($log->status == 'failed')
                                    <span class="px-3 py-1.5 bg-red-100 text-red-800 text-xs font-semibold rounded-full flex items-center w-fit shadow-sm">
                                        <span class="material-icons text-red-600 mr-1" style="font-size: 16px;">cancel</span> Failed
                                    </span>
                                @elseif($log->status == 'locked')
                                    <span class="px-3 py-1.5 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full flex items-center w-fit shadow-sm">
                                        <span class="material-icons text-orange-600 mr-1" style="font-size: 16px;">lock</span> Locked
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full shadow-sm">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->failure_reason)
                                    <span class="text-red-600 text-sm font-medium">
                                        {{ ucwords(str_replace('_', ' ', $log->failure_reason)) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->attempts_count > 0)
                                    <span class="px-3 py-1.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full shadow-sm">
                                        {{ $log->attempts_count }} / 5
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center">
                                        <span class="material-icons {{ $browserColor }} mr-1" style="font-size: 16px;">{{ $browserIcon }}</span>
                                        <span class="text-sm font-medium text-gray-800">{{ $browser }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="material-icons text-gray-400 mr-1" style="font-size: 16px;">{{ $osIcon }}</span>
                                        <span class="text-xs text-gray-500">{{ $os }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->locked_until)
                                    <div class="text-sm">
                                        <div class="text-orange-600 font-semibold flex items-center">
                                            <span class="material-icons mr-1" style="font-size: 16px;">event</span>
                                            {{ \Carbon\Carbon::parse($log->locked_until)->timezone('Asia/Manila')->format('M d, Y') }}
                                        </div>
                                        <div class="text-gray-600 ml-5">
                                            {{ \Carbon\Carbon::parse($log->locked_until)->timezone('Asia/Manila')->format('h:i A') }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="bg-green-100 p-6 rounded-full mb-4">
                                        <span class="material-icons text-green-600" style="font-size: 64px;">security</span>
                                    </div>
                                    <p class="text-xl font-bold text-gray-700 mb-2">No Login Audit Logs Found</p>
                                    <p class="text-sm text-gray-500">Login attempts will appear here once users start accessing the system.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditLogs->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection