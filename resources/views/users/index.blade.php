@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <span class="material-icons text-green-600 text-4xl mr-3">manage_accounts</span>
                <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('users.archived') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center">
                    <span class="material-icons mr-2">inventory_2</span>
                    Archived
                </a>
                <a href="{{ route('users.create-cashier') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center">
                    <span class="material-icons mr-2">person_add</span>
                    Create Cashier
                </a>
                <a href="{{ route('users.create-manager') }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center">
                    <span class="material-icons mr-2">admin_panel_settings</span>
                    Create Manager
                </a>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <input type="text" id="searchInput" placeholder="Search users..."
                   class="w-full max-w-md px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-green-700 to-green-600 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Created</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @forelse($users as $user)
                        <tr class="border-b hover:bg-green-50 transition"
                            data-name="{{ strtolower($user->fname . ' ' . $user->lname) }}"
                            data-email="{{ strtolower($user->email) }}"
                            data-contact="{{ $user->contact_number ?? '' }}"
                            data-role="{{ strtolower($user->role) }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-white text-sm mr-3
                                        {{ $user->role === 'admin' ? 'bg-red-500' : '' }}
                                        {{ $user->role === 'manager' ? 'bg-blue-500' : '' }}
                                        {{ $user->role === 'cashier' ? 'bg-green-500' : '' }}">
                                        {{ substr($user->fname, 0, 1) }}{{ substr($user->lname, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-sm">{{ $user->fname }} {{ $user->lname }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm">{{ $user->contact_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $user->role === 'admin'   ? 'bg-red-100 text-red-800'   : '' }}
                                    {{ $user->role === 'manager' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $user->role === 'cashier' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($user->role !== 'admin')
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('users.edit', $user->User_ID) }}"
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm flex items-center">
                                            <span class="material-icons text-sm mr-1">edit</span>
                                            Edit
                                        </a>
                                        <button type="button"
                                                onclick="openArchiveModal('{{ $user->User_ID }}', '{{ $user->fname }} {{ $user->lname }}', '{{ ucfirst($user->role) }}')"
                                                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-sm flex items-center">
                                            <span class="material-icons text-sm mr-1">inventory_2</span>
                                            Archive
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Protected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="noResults">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex gap-4 text-sm text-gray-600">
            <div class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>Admin: {{ $users->where('role', 'admin')->count() }}</div>
            <div class="flex items-center"><span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>Manager: {{ $users->where('role', 'manager')->count() }}</div>
            <div class="flex items-center"><span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>Cashier: {{ $users->where('role', 'cashier')->count() }}</div>
            <div class="ml-auto font-semibold">Total: {{ $users->count() }}</div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div id="archiveModal" class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-5 flex items-center">
            <div class="bg-white bg-opacity-20 p-2 rounded-full mr-3">
                <span class="material-icons text-white" style="font-size:28px;">inventory_2</span>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Confirm Archive</h3>
                <p class="text-green-100 text-sm">This user will be moved to the archive</p>
            </div>
        </div>
        <div class="px-6 py-6">
            <div class="flex items-start">
                <div class="bg-orange-100 p-3 rounded-full mr-4 flex-shrink-0">
                    <span class="material-icons text-orange-600" style="font-size:28px;">warning_amber</span>
                </div>
                <div>
                    <p class="text-gray-700 font-semibold text-base mb-1">Are you sure you want to archive?</p>
                    <p class="text-gray-500 text-sm">You are about to archive:</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span id="archive_role_badge" class="px-2 py-0.5 text-xs font-bold rounded-full"></span>
                        <span id="archive_user_name" class="font-bold text-gray-900 text-sm"></span>
                    </div>
                    <p class="text-gray-400 text-xs mt-2">They will lose access but can be restored later.</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
            <button type="button" onclick="closeArchiveModal()"
                    class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition flex items-center">
                <span class="material-icons mr-1" style="font-size:16px;">close</span>
                Cancel
            </button>
            <form id="archiveForm" method="POST" action="">
                @csrf
                <button type="submit"
                        class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow transition flex items-center">
                    <span class="material-icons mr-1" style="font-size:16px;">inventory_2</span>
                    Yes, Archive
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openArchiveModal(id, name, role) {
    document.getElementById('archiveForm').action = `/users/${id}/archive`;
    document.getElementById('archive_user_name').textContent = name;
    const badge = document.getElementById('archive_role_badge');
    badge.textContent = role;
    badge.className = role === 'Manager'
        ? 'px-2 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-800'
        : 'px-2 py-0.5 text-xs font-bold rounded-full bg-green-100 text-green-800';
    const modal = document.getElementById('archiveModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeArchiveModal() {
    const modal = document.getElementById('archiveModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('archiveModal').addEventListener('click', function(e) {
    if (e.target === this) closeArchiveModal();
});

document.getElementById('searchInput').addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    let count = 0;
    document.querySelectorAll('#usersTableBody tr:not(#noResults)').forEach(row => {
        const match = ['name','email','contact','role'].some(a => (row.dataset[a] || '').includes(term));
        row.style.display = match ? '' : 'none';
        if (match) count++;
    });
    const noResults = document.getElementById('noResults');
    if (noResults) noResults.style.display = count === 0 && term ? '' : 'none';
});
</script>
@endsection