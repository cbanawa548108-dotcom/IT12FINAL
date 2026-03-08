@extends('layouts.app')

@section('title', 'Create Cashier Account')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-6">
            <span class="material-icons text-green-600 text-4xl mr-3">person_add</span>
            <h2 class="text-2xl font-bold text-gray-800">Create Cashier Account</h2>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store-cashier') }}" method="POST" class="space-y-4">
            @csrf

            <!-- First Name -->
            <div>
                <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="fname" id="fname" value="{{ old('fname') }}" required
                    maxlength="255"
                    pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-]+"
                    title="First name should only contain letters, spaces, or hyphens"
                    oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ\s\-]/g, '')"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Last Name -->
            <div>
                <label for="lname" class="block text-sm font-medium text-gray-700 mb-1">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="lname" id="lname" value="{{ old('lname') }}" required
                    maxlength="255"
                    pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-]+"
                    title="Last name should only contain letters, spaces, or hyphens"
                    oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ\s\-]/g, '')"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Contact Number -->
            <div>
                <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">
                    Contact Number
                </label>
                <input type="tel" name="contact_number" id="contact_number" value="{{ old('contact_number') }}"
                    placeholder="09xxxxxxxxx"
                    pattern="[0-9]{11}"
                    maxlength="11"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                    title="Please enter an 11-digit contact number"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="cashier@fruitstand.com"
                    maxlength="255"
                    title="Please enter a valid email address"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" required
                    placeholder="Minimum 16 characters with special character"
                    minlength="16"
                    title="Password must be at least 16 characters with uppercase, lowercase, number, and special character"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <p id="password-strength-msg" class="text-xs mt-1"></p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    minlength="16"
                    title="Please confirm your password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <p id="password-match-msg" class="text-xs mt-1 hidden"></p>
            </div>

            <!-- Role Display (Read-only) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                    Cashier
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center">
                    <span class="material-icons mr-2">save</span>
                    Create Cashier Account
                </button>
                <a href="{{ route('users.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center">
                    <span class="material-icons mr-2">cancel</span>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Password strength check
    const password = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const msg = document.getElementById('password-match-msg');

    function checkPasswordStrength(val) {
        const checks = [
            { regex: /.{16,}/, label: '16+ characters' },
            { regex: /[A-Z]/, label: 'uppercase letter' },
            { regex: /[a-z]/, label: 'lowercase letter' },
            { regex: /[0-9]/, label: 'number' },
            { regex: /[^A-Za-z0-9]/, label: 'special character' },
        ];
        const failed = checks.filter(c => !c.regex.test(val)).map(c => c.label);
        return failed;
    }

    password.addEventListener('input', function () {
        const failed = checkPasswordStrength(this.value);
        const strengthEl = document.getElementById('password-strength-msg');
        if (!strengthEl) return;
        if (this.value === '') { strengthEl.textContent = ''; return; }
        if (failed.length === 0) {
            strengthEl.textContent = '✓ Strong password';
            strengthEl.className = 'text-xs mt-1 text-green-600';
        } else {
            strengthEl.textContent = '✗ Missing: ' + failed.join(', ');
            strengthEl.className = 'text-xs mt-1 text-red-600';
        }
        checkPasswordMatch();
    });

    confirm.addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        if (confirm.value === '') { msg.classList.add('hidden'); return; }
        if (password.value === confirm.value) {
            msg.textContent = '✓ Passwords match';
            msg.className = 'text-xs mt-1 text-green-600';
        } else {
            msg.textContent = '✗ Passwords do not match';
            msg.className = 'text-xs mt-1 text-red-600';
        }
    }
</script>
@endsection