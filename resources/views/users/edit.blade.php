@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white text-lg mr-4
                {{ $user->role === 'admin'   ? 'bg-red-500'   : '' }}
                {{ $user->role === 'manager' ? 'bg-blue-500'  : '' }}
                {{ $user->role === 'cashier' ? 'bg-green-500' : '' }}">
                {{ substr($user->fname, 0, 1) }}{{ substr($user->lname, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
                <p class="text-gray-500 text-sm">{{ $user->fname }} {{ $user->lname }} — {{ ucfirst($user->role) }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user->User_ID) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">First Name</label>
                    <input type="text" name="fname" value="{{ old('fname', $user->fname) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Last Name</label>
                    <input type="text" name="lname" value="{{ old('lname', $user->lname) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}"
                       placeholder="e.g. 09123456789"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('users.index') }}"
                   class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition flex items-center">
                    <span class="material-icons mr-1" style="font-size:16px;">close</span>
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition flex items-center">
                    <span class="material-icons mr-1" style="font-size:16px;">save</span>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection