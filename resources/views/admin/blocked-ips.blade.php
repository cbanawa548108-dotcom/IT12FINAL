@extends('layouts.app')

@section('title', 'Blocked IPs')
@section('page_title', 'IP Blocking Management')

@section('content')
<div class="container mx-auto px-4">

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center">
            <span class="material-icons mr-2 text-green-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
            @foreach($errors->all() as $error)
                <p class="flex items-center">
                    <span class="material-icons mr-2 text-red-600" style="font-size:18px;">error</span>{{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg shadow-md border border-green-200">
            <div class="flex items-center">
                <div class="bg-green-600 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size: 28px;">security</span>
                </div>
                <div>
                    <div class="text-xs text-green-700 font-semibold uppercase tracking-wide">Total Blocked</div>
                    <div class="text-3xl font-bold text-green-900">{{ $blockedIps->total() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg shadow-md border border-blue-200">
            <div class="flex items-center">
                <div class="bg-blue-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size: 28px;">person_off</span>
                </div>
                <div>
                    <div class="text-xs text-blue-700 font-semibold uppercase tracking-wide">Blocked Users</div>
                    <div class="text-3xl font-bold text-blue-900">{{ $blockedIps->getCollection()->whereNotNull('user_id')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg shadow-md border border-orange-200">
            <div class="flex items-center">
                <div class="bg-orange-500 p-3 rounded-full mr-4">
                    <span class="material-icons text-white" style="font-size: 28px;">router</span>
                </div>
                <div>
                    <div class="text-xs text-orange-700 font-semibold uppercase tracking-wide">Blocked IPs</div>
                    <div class="text-3xl font-bold text-orange-900">{{ $blockedIps->getCollection()->whereNull('user_id')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Block Form -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="material-icons mr-2 text-green-700">add_circle</span>
            Block a User or IP Address
        </h2>

        <form method="POST" action="{{ route('blocked-ips.store') }}">
            @csrf
            <div class="flex gap-4 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="block_type" value="user" id="block_user"
                           class="accent-green-600" checked
                           onchange="document.getElementById('user_fields').classList.remove('hidden'); document.getElementById('ip_fields').classList.add('hidden');">
                    <span class="font-semibold text-gray-700">Block Specific User</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="block_type" value="ip" id="block_ip"
                           class="accent-green-600"
                           onchange="document.getElementById('ip_fields').classList.remove('hidden'); document.getElementById('user_fields').classList.add('hidden');">
                    <span class="font-semibold text-gray-700">Block by IP Address</span>
                </label>
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <div id="user_fields" class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Select User</label>
                    <select name="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <option value="">-- Select a user --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->User_ID }}">
                                {{ $user->fname }} {{ $user->lname }} ({{ $user->email }}) — {{ ucfirst($user->role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="ip_fields" class="flex-1 hidden">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">IP Address</label>
                    <input type="text" name="ip_address" placeholder="e.g. 192.168.1.1"
                           value="{{ old('ip_address') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Reason (optional)</label>
                    <input type="text" name="reason" placeholder="e.g. Suspicious activity"
                           value="{{ old('reason') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="px-6 py-2 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-lg shadow transition flex items-center">
                        <span class="material-icons mr-2" style="font-size:18px;">block</span>
                        Block
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Blocked List Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-4 flex items-center justify-between">
            <h2 class="text-white font-bold text-lg flex items-center">
                <span class="material-icons mr-2">security</span>
                Blocked Users & IPs
            </h2>
            <span class="bg-white text-green-700 text-sm font-bold px-3 py-1 rounded-full">
                {{ $blockedIps->total() }} Blocked
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-green-700 to-green-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Type</th>
                        <th class="px-6 py-3 text-left font-semibold">Blocked Target</th>
                        <th class="px-6 py-3 text-left font-semibold">Reason</th>
                        <th class="px-6 py-3 text-left font-semibold">Blocked By</th>
                        <th class="px-6 py-3 text-left font-semibold">Date Blocked</th>
                        <th class="px-6 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($blockedIps as $blocked)
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-6 py-4">
                                @if($blocked->user_id)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full flex items-center w-fit">
                                        <span class="material-icons mr-1" style="font-size:14px;">person_off</span>
                                        User
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-bold rounded-full flex items-center w-fit">
                                        <span class="material-icons mr-1" style="font-size:14px;">router</span>
                                        IP
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($blocked->user_id && $blocked->blockedUser)
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 bg-gradient-to-br from-green-600 to-green-700 text-white rounded-full flex items-center justify-center font-bold text-sm mr-2 shadow">
                                            {{ substr($blocked->blockedUser->fname, 0, 1) }}{{ substr($blocked->blockedUser->lname, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 text-sm">{{ $blocked->blockedUser->fname }} {{ $blocked->blockedUser->lname }}</div>
                                            <div class="text-xs text-gray-500">{{ $blocked->blockedUser->email }}</div>
                                            <div class="text-xs text-gray-400 font-mono">{{ $blocked->ip_address }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <span class="material-icons text-green-600 mr-2" style="font-size:18px;">router</span>
                                        <span class="font-mono font-bold text-gray-900 bg-green-50 border border-green-200 px-2 py-1 rounded text-sm">
                                            {{ $blocked->ip_address }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($blocked->reason)
                                    <div class="flex items-center">
                                        <span class="material-icons text-orange-500 mr-1" style="font-size:16px;">warning</span>
                                        <span class="text-gray-700 text-sm">{{ $blocked->reason }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($blocked->blocker)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-green-700 text-white rounded-full flex items-center justify-center font-bold text-sm mr-2 shadow">
                                            {{ substr($blocked->blocker->fname, 0, 1) }}{{ substr($blocked->blocker->lname, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-gray-800 font-medium text-sm">{{ $blocked->blocker->fname }} {{ $blocked->blocker->lname }}</div>
                                            <div class="text-xs text-gray-500">{{ ucfirst($blocked->blocker->role) }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-400 text-white rounded-full flex items-center justify-center mr-2">
                                            <span class="material-icons" style="font-size:16px;">smart_toy</span>
                                        </div>
                                        <span class="text-gray-500 text-sm font-medium">Auto-blocked</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($blocked->created_at)->timezone('Asia/Manila')->format('M d, Y') }}
                                </div>
                                <div class="text-gray-500">
                                    {{ \Carbon\Carbon::parse($blocked->created_at)->timezone('Asia/Manila')->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button"
                                        onclick="openUnblockModal(
                                            '{{ $blocked->id }}',
                                            '{{ $blocked->user_id && $blocked->blockedUser ? $blocked->blockedUser->fname . ' ' . $blocked->blockedUser->lname : $blocked->ip_address }}',
                                            '{{ $blocked->user_id ? 'user' : 'ip' }}'
                                        )"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow transition flex items-center">
                                    <span class="material-icons mr-1" style="font-size:16px;">lock_open</span>
                                    Unblock
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="bg-green-100 p-6 rounded-full mb-4">
                                        <span class="material-icons text-green-600" style="font-size:64px;">verified_user</span>
                                    </div>
                                    <p class="text-xl font-bold text-gray-700 mb-2">No Blocked Users or IPs</p>
                                    <p class="text-sm text-gray-500">Nothing has been blocked yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($blockedIps->hasPages())
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $blockedIps->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ── Unblock Confirmation Modal ─────────────────────────── -->
<div id="unblockModal"
     class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">

        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-5 flex items-center">
            <div class="bg-white bg-opacity-20 p-2 rounded-full mr-3">
                <span class="material-icons text-white" style="font-size:28px;">lock_open</span>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Confirm Unblock</h3>
                <p class="text-green-100 text-sm">This action will restore access</p>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-6">
            <div class="flex items-start mb-4">
                <div class="bg-yellow-100 p-3 rounded-full mr-4 flex-shrink-0">
                    <span class="material-icons text-yellow-600" style="font-size:28px;">warning_amber</span>
                </div>
                <div>
                    <p class="text-gray-700 font-semibold text-base mb-1">Are you sure you want to unblock?</p>
                    <p class="text-gray-500 text-sm">You are about to restore access for:</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span id="modal_type_badge" class="px-2 py-0.5 text-xs font-bold rounded-full"></span>
                        <span id="modal_target_name" class="font-bold text-gray-900 text-sm"></span>
                    </div>
                    <p class="text-gray-400 text-xs mt-2">They will immediately regain access to the system.</p>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
            <button type="button"
                    onclick="closeUnblockModal()"
                    class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition flex items-center">
                <span class="material-icons mr-1" style="font-size:16px;">close</span>
                Cancel
            </button>
            <form id="unblockForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition flex items-center">
                    <span class="material-icons mr-1" style="font-size:16px;">lock_open</span>
                    Yes, Unblock
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openUnblockModal(id, name, type) {
        const modal = document.getElementById('unblockModal');
        const form  = document.getElementById('unblockForm');
        const badge = document.getElementById('modal_type_badge');
        const label = document.getElementById('modal_target_name');

        form.action = `/blocked-ips/${id}`;
        label.textContent = name;

        if (type === 'user') {
            badge.textContent = 'User';
            badge.className = 'px-2 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-800';
        } else {
            badge.textContent = 'IP';
            badge.className = 'px-2 py-0.5 text-xs font-bold rounded-full bg-orange-100 text-orange-800';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeUnblockModal() {
        const modal = document.getElementById('unblockModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on backdrop click
    document.getElementById('unblockModal').addEventListener('click', function(e) {
        if (e.target === this) closeUnblockModal();
    });
</script>

@endsection