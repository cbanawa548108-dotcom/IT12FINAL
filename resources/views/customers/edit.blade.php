@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-green-700">Edit Customer</h1>

@if ($errors->any())
<div class="bg-red-100 text-red-700 p-2 rounded mb-4">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('customers.update', $customer->Customer_ID) }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label>Customer Name</label>
        <input type="text" name="Customer_Name" value="{{ old('Customer_Name', $customer->Customer_Name) }}"
            class="w-full border p-2 rounded"
            maxlength="255"
            required>
    </div>
    <div class="mb-4">
        <label>Contact Number</label>
        <input type="tel" name="Contact_Number" id="Contact_Number"
            class="w-full border p-2 rounded @error('Contact_Number') border-red-500 @enderror"
            pattern="09[0-9]{9}"
            maxlength="11"
            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11); validatePhone(this)"
            placeholder="e.g. 09123456789"
            title="Contact number must start with 09 and be 11 digits"
            value="{{ old('Contact_Number', $customer->Contact_Number) }}">
        <p id="phoneError" class="text-red-600 text-sm mt-1 hidden">
            Contact number must start with <strong>09</strong> and be exactly 11 digits.
        </p>
        @error('Contact_Number')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <button type="submit" id="submitBtn"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Update Customer
    </button>
</form>

<script>
function validatePhone(input) {
    const val = input.value;
    const errorEl = document.getElementById('phoneError');
    const submitBtn = document.getElementById('submitBtn');

    // Only validate if something has been typed
    if (val.length === 0) {
        errorEl.classList.add('hidden');
        input.classList.remove('border-red-500');
        submitBtn.disabled = false;
        return;
    }

    const isValid = /^09[0-9]{9}$/.test(val);
    const startsCorrectly = val.startsWith('09') || val.length < 2;

    if (!startsCorrectly || (val.length === 11 && !isValid)) {
        errorEl.classList.remove('hidden');
        input.classList.add('border-red-500');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        errorEl.classList.add('hidden');
        input.classList.remove('border-red-500');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Also block form submission if invalid
document.querySelector('form').addEventListener('submit', function(e) {
    const input = document.getElementById('Contact_Number');
    const val = input.value;

    if (val.length > 0 && !/^09[0-9]{9}$/.test(val)) {
        e.preventDefault();
        document.getElementById('phoneError').classList.remove('hidden');
        input.classList.add('border-red-500');
        input.focus();
    }
});
</script>
@endsection