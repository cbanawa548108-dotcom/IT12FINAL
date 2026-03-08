@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-2xl shadow-lg">

    <h1 class="text-3xl font-bold text-green-700 mb-6">Add Supplier</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-4" id="supplierForm" novalidate>
        @csrf

        <!-- Supplier Name -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Supplier Name</label>
            <input type="text" name="Supplier_Name" id="Supplier_Name"
                   value="{{ old('Supplier_Name') }}"
                   placeholder="Enter supplier name"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            <p class="text-red-500 text-sm mt-1 hidden" id="err_Supplier_Name">
                Supplier name is required (letters only, min 2 characters).
            </p>
        </div>

        <!-- Contact Person -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Contact Person</label>
            <input type="text" name="contact_person" id="contact_person"
                   value="{{ old('contact_person') }}"
                   placeholder="Enter contact person name"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            <p class="text-red-500 text-sm mt-1 hidden" id="err_contact_person">
                Contact person name is required (letters only, min 2 characters).
            </p>
        </div>

        <!-- Contact Number -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number"
                   value="{{ old('contact_number') }}"
                   placeholder="09XXXXXXXXX"
                   maxlength="11"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            <p class="text-red-500 text-sm mt-1 hidden" id="err_contact_number">
                Contact number must be exactly 11 digits and start with 09 (e.g. 09171234567).
            </p>
        </div>

        <!-- Address -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Address</label>
            <input type="text" name="address" id="address"
                   value="{{ old('address') }}"
                   placeholder="Enter complete address"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            <p class="text-red-500 text-sm mt-1 hidden" id="err_address">
                Address is required (min 5 characters).
            </p>
        </div>

        <!-- Payment Terms -->
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Payment Terms</label>
            <select name="payment_terms" id="payment_terms"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                <option value="">Select Payment Terms</option>
                <option value="Cash" {{ old('payment_terms') == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="GCash" {{ old('payment_terms') == 'GCash' ? 'selected' : '' }}>GCash</option>
            </select>
            <p class="text-red-500 text-sm mt-1 hidden" id="err_payment_terms">
                Please select a payment term.
            </p>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('suppliers.index') }}"
               class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold shadow-md transition">
                Cancel
            </a>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                Save Supplier
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('supplierForm');

    // Show or hide error message and highlight input
    function showError(id, show) {
        const el = document.getElementById('err_' + id);
        const input = document.getElementById(id);
        if (show) {
            el.classList.remove('hidden');
            input.classList.add('border-red-500', 'bg-red-50');
        } else {
            el.classList.add('hidden');
            input.classList.remove('border-red-500', 'bg-red-50');
        }
    }

    // Validate name fields: letters, spaces, dots, hyphens, apostrophes, min 2 chars
    function validateName(id) {
        const val = document.getElementById(id).value.trim();
        const valid = val.length >= 2 && /^[a-zA-Z\s\.\-']+$/.test(val);
        showError(id, !valid);
        return valid;
    }

    // Validate contact number: exactly 11 digits, starts with 09
    function validatePhone() {
        const val = document.getElementById('contact_number').value.trim();
        const valid = /^09\d{9}$/.test(val);
        showError('contact_number', !valid);
        return valid;
    }

    // Validate address: min 5 characters
    function validateAddress() {
        const val = document.getElementById('address').value.trim();
        const valid = val.length >= 5;
        showError('address', !valid);
        return valid;
    }

    // Validate payment terms: must select one
    function validatePayment() {
        const val = document.getElementById('payment_terms').value;
        const valid = val !== '';
        showError('payment_terms', !valid);
        return valid;
    }

    // Only allow numeric input for contact number
    document.getElementById('contact_number').addEventListener('keydown', (e) => {
        const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
        if (!allowed.includes(e.key) && !/^\d$/.test(e.key)) {
            e.preventDefault();
        }
    });

    // Live validation on blur (when leaving a field)
    document.getElementById('Supplier_Name').addEventListener('blur', () => validateName('Supplier_Name'));
    document.getElementById('contact_person').addEventListener('blur', () => validateName('contact_person'));
    document.getElementById('contact_number').addEventListener('blur', validatePhone);
    document.getElementById('address').addEventListener('blur', validateAddress);
    document.getElementById('payment_terms').addEventListener('change', validatePayment);

    // Clear error as soon as user starts typing again
    ['Supplier_Name', 'contact_person', 'contact_number', 'address'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => {
            showError(id, false);
        });
    });

    // Final validation on form submit
    form.addEventListener('submit', (e) => {
        const v1 = validateName('Supplier_Name');
        const v2 = validateName('contact_person');
        const v3 = validatePhone();
        const v4 = validateAddress();
        const v5 = validatePayment();

        if (!v1 || !v2 || !v3 || !v4 || !v5) {
            e.preventDefault();
            // Scroll to first highlighted error
            const firstError = form.querySelector('.border-red-500');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection