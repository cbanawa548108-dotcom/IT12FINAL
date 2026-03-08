@extends('layouts.app')

@section('title', 'Archived Users')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <span class="material-icons text-gray-500 text-4xl mr-3">inventory_2</span>
                <h2 class="text-2xl font-bold text-gray-800">Archived Users</h2>
            </div>
            <a href="{{ route('users.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center">
                <span class="material-icons mr-2">arrow_back</span>
                Back to Users
            </a>
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
                    <tr class="bg-gradient-to-r from-gray-600 to-gray-500 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Archived On</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 rounded-full bg-gray-400 text-white flex items-center justify-center font-bold text-sm mr-3">
                                        {{ substr($user->fname, 0, 1) }}{{ substr($user->lname, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-sm text-gray-600">{{ $user->fname }} {{ $user->lname }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($user->deleted_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('users.restore', $user->User_ID) }}">
                                        @csrf
                                        <button type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                            <span class="material-icons text-sm mr-1">restore</span>
                                            Restore
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="openDeleteModal('{{ $user->User_ID }}', '{{ $user->fname }} {{ $user->lname }}')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm flex items-center">
                                        <span class="material-icons text-sm mr-1">delete_forever</span>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <span class="material-icons text-6xl text-gray-300 mb-2">inventory_2</span>
                                    <p class="text-lg font-semibold">No archived users</p>
                                    <p class="text-sm">Archived users will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Forever Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-red-700 to-red-600 px-6 py-5 flex items-center">
            <div class="bg-white bg-opacity-20 p-2 rounded-full mr-3">
                <span class="material-icons text-white" style="font-size:28px;">delete_forever</span>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Permanent Delete</h3>
                <p class="text-red-100 text-sm">This action cannot be undone</p>
            </div>
        </div>
        <div class="px-6 py-6">
            <div class="flex items-start">
                <div class="bg-red-100 p-3 rounded-full mr-4 flex-shrink-0">
                    <span class="material-icons text-red-600" style="font-size:28px;">warning_amber</span>
                </div>
                <div>
                    <p class="text-gray-700 font-semibold mb-1">Permanently delete this user?</p>
                    <p class="text-gray-500 text-sm">You are about to permanently delete:</p>
                    <p id="delete_user_name" class="font-bold text-gray-900 text-sm mt-2"></p>
                    <p class="text-red-500 text-xs mt-2">This cannot be recovered.</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()"
                    class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition flex items-center">
                <span class="material-icons mr-1" style="font-size:16px;">close</span>
                Cancel
            </button>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition flex items-center">
                    <span class="material-icons mr-1" style="font-size:16px;">delete_forever</span>
                    Yes, Delete Forever
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteForm').action = `/users/${id}`;
    document.getElementById('delete_user_name').textContent = name;
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endsection