<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Code - CRM FruitStand</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .glass {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
      border-radius: 1.5rem;
      border: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 20px 60px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.2);
      position: relative; overflow: hidden;
  }
  .otp-input {
      width: 3rem; height: 3.5rem;
      text-align: center; font-size: 1.5rem; font-weight: 700;
      border: 2px solid #d1d5db; border-radius: 0.75rem;
      background: rgba(255,255,255,0.85);
      transition: all 0.3s ease;
  }
  .otp-input:focus {
      outline: none; border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16,185,129,0.25);
  }
  .btn-verify {
      background: linear-gradient(135deg, #064630ff, #02130dff);
      color: white; font-weight: 600; padding: 0.85rem;
      border-radius: 0.75rem; width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  }
  .btn-verify:hover {
      background: linear-gradient(135deg, #112e25ff, #14523dff);
      transform: translateY(-2px);
  }
  .sticker {
      width: 130px; height: 130px;
      background: url('https://i.pinimg.com/1200x/c3/60/70/c360704c2fc4da0a806b1972407ca80b.jpg') center/cover no-repeat;
      border-radius: 50%; border: 4px solid rgba(255,255,255,0.7);
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
      animation: float 4s ease-in-out infinite alternate;
  }
  @keyframes float {
      0%   { transform: translateY(0) rotate(-2deg); }
      100% { transform: translateY(-10px) rotate(3deg); }
  }
</style>
</head>
<body class="bg-gradient-to-br from-green-100 via-green-200 to-green-300 h-screen flex items-center justify-center">

<div class="flex flex-col md:flex-row w-full max-w-6xl rounded-3xl overflow-hidden shadow-2xl">

  <!-- Left Panel -->
  <div class="md:w-1/2 bg-gradient-to-tr from-green-700 to-green-800 text-white flex flex-col items-center justify-center p-12">
    <div class="flex flex-col items-center text-center space-y-6">
      <div class="sticker"></div>
      <h1 class="text-5xl font-extrabold" style="text-shadow:2px 6px 15px rgba(0,0,0,0.4)">CRM FruitStand</h1>
      <p class="text-lg text-green-100 leading-relaxed">Step 2 of 3 — Enter the 6-digit code sent to your email.</p>

      <div class="flex items-center space-x-2 mt-4">
        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-sm">✓</div>
        <div class="w-12 h-1 bg-green-400 rounded"></div>
        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-green-700 font-bold text-sm">2</div>
        <div class="w-12 h-1 bg-green-900 rounded"></div>
        <div class="w-8 h-8 rounded-full bg-green-900 flex items-center justify-center text-green-400 font-bold text-sm">3</div>
      </div>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="md:w-1/2 flex items-center justify-center p-10 bg-white">
    <div class="w-full max-w-md glass p-10">
      <h2 class="text-3xl font-bold text-gray-700 mb-2 text-center">Check Your Email</h2>
      <p class="text-gray-500 text-center text-sm mb-6">
        We sent a 6-digit code to<br>
        <span class="font-semibold text-green-700">{{ session('two_factor_email') }}</span>
      </p>

      @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center font-medium">
          {{ $errors->first() }}
        </div>
      @endif

      @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center font-medium">
          {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('two-factor.verify') }}" method="POST" id="otpForm" class="space-y-6">
        @csrf

        <div class="flex justify-center gap-3">
          <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
          <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
          <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
          <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
          <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
          <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
        </div>

        <input type="hidden" name="code" id="codeField">

        <button type="submit" class="btn-verify">Verify Code</button>
      </form>

      <form action="{{ route('two-factor.resend') }}" method="POST" class="mt-4 text-center">
        @csrf
        <span class="text-gray-400 text-sm">Didn't receive it? </span>
        <button type="submit" class="text-green-600 hover:text-green-800 font-semibold text-sm underline">
          Resend Code
        </button>
      </form>

      <p class="text-center text-gray-400 mt-6 text-sm">&copy; {{ date('Y') }} CRM FruitStand. All rights reserved.</p>
    </div>
  </div>
</div>

<script>
  const inputs  = document.querySelectorAll('.otp-input');
  const codeField = document.getElementById('codeField');

  inputs.forEach((input, i) => {
    input.addEventListener('input', e => {
      e.target.value = e.target.value.replace(/\D/g, '');
      if (e.target.value && i < inputs.length - 1) inputs[i + 1].focus();
      updateCode();
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !e.target.value && i > 0) inputs[i - 1].focus();
    });
    input.addEventListener('paste', e => {
      const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
      if (pasted.length === 6) {
        e.preventDefault();
        pasted.split('').forEach((char, idx) => { if (inputs[idx]) inputs[idx].value = char; });
        inputs[5].focus();
        updateCode();
      }
    });
  });

  function updateCode() {
    codeField.value = Array.from(inputs).map(i => i.value).join('');
  }

  document.getElementById('otpForm').addEventListener('submit', function(e) {
    updateCode();
    if (codeField.value.length < 6) {
      e.preventDefault();
      alert('Please enter all 6 digits.');
    }
  });
</script>
</body>
</html>