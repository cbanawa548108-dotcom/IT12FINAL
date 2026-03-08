<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - CRM FruitStand</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .glass {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border-radius: 1.5rem;
      border: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 20px 60px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.2);
      position: relative;
      overflow: hidden;
  }
  .glass::before {
      content: '';
      position: absolute;
      top: -50%; left: -50%;
      width: 200%; height: 200%;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.15), transparent 70%);
      pointer-events: none;
  }
  .form-group { position: relative; }

  /* Default input */
  .form-input {
      width: 100%;
      padding: 1.1rem 3rem 0.5rem 1rem;
      border-radius: 0.75rem;
      border: 1px solid #d1d5db;
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(12px);
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: inset 0 2px 6px rgba(0,0,0,0.08);
  }
  .form-input:focus {
      outline: none;
      border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16,185,129,0.25), inset 0 2px 6px rgba(0,0,0,0.08);
  }

  /* Error state */
  .form-input.input-error {
      border-color: #ef4444 !important;
      box-shadow: 0 0 0 3px rgba(239,68,68,0.18), inset 0 2px 6px rgba(0,0,0,0.08) !important;
  }

  /* Floating label */
  .form-label {
      position: absolute;
      left: 1rem; top: 1rem;
      color: #6b7280; font-size: 1rem;
      pointer-events: none;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.85);
      padding: 0 0.25rem;
      z-index: 1;
  }
  .form-input:focus + .form-label,
  .form-input:not(:placeholder-shown) + .form-label {
      top: -0.5rem; font-size: 0.75rem; color: #10b981;
  }
  .form-input.input-error + .form-label,
  .form-input.input-error:focus + .form-label,
  .form-input.input-error:not(:placeholder-shown) + .form-label {
      color: #ef4444;
  }

  /* Show/hide password button */
  .toggle-password {
      position: absolute;
      right: 0.85rem;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #9ca3af;
      transition: color 0.2s ease;
      background: none;
      border: none;
      padding: 0.25rem;
      display: flex;
      align-items: center;
      z-index: 10;
  }
  .toggle-password:hover { color: #10b981; }
  .toggle-password:focus { outline: none; }

  /* Inline field error */
  .field-error {
      display: flex;
      align-items: center;
      gap: 0.35rem;
      margin-top: 0.4rem;
      padding: 0.45rem 0.65rem;
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-radius: 0.5rem;
      font-size: 0.78rem;
      color: #b91c1c;
      font-weight: 500;
      line-height: 1.4;
  }
  .field-error svg { flex-shrink: 0; }

  /* Success alert */
  .alert-success {
      display: flex;
      align-items: flex-start;
      gap: 0.6rem;
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-left: 4px solid #10b981;
      color: #065f46;
      padding: 0.75rem 1rem;
      border-radius: 0.65rem;
      font-size: 0.875rem;
      font-weight: 500;
  }

  /* Rate-limit / lockout banner */
  .alert-lockout {
      display: flex;
      align-items: flex-start;
      gap: 0.6rem;
      background: #fff7ed;
      border: 1px solid #fed7aa;
      border-left: 4px solid #f97316;
      color: #9a3412;
      padding: 0.75rem 1rem;
      border-radius: 0.65rem;
      font-size: 0.875rem;
      font-weight: 500;
  }

  /* Sticker */
  .sticker {
      width: 130px; height: 130px;
      background: url('https://i.pinimg.com/1200x/c3/60/70/c360704c2fc4da0a806b1972407ca80b.jpg') center/cover no-repeat;
      border-radius: 50%;
      border: 4px solid rgba(255,255,255,0.7);
      box-shadow: 0 20px 40px rgba(0,0,0,0.25), inset 0 6px 12px rgba(255,255,255,0.2);
      animation: float 4s ease-in-out infinite alternate;
      position: relative;
  }
  .sticker::after {
      content: '';
      position: absolute;
      top: 10%; left: 10%;
      width: 80%; height: 80%;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.4), transparent 60%);
      pointer-events: none;
  }
  @keyframes float {
      0%   { transform: translateY(0) rotate(-2deg); }
      100% { transform: translateY(-10px) rotate(3deg); }
  }

  /* Login button */
  .btn-login {
      background: linear-gradient(135deg, #064630ff, #02130dff);
      color: white; font-weight: 600; padding: 0.85rem;
      border-radius: 0.75rem; width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      cursor: pointer;
  }
  .btn-login:hover {
      background: linear-gradient(135deg, #112e25ff, #14523dff);
      box-shadow: 0 15px 35px rgba(0,0,0,0.25);
      transform: translateY(-2px);
  }
  .forgot-link {
      color: #059669; text-decoration: none;
      font-size: 0.875rem; font-weight: 500;
      transition: all 0.3s ease;
  }
  .forgot-link:hover { color: #047857; text-decoration: underline; }
</style>
</head>
<body class="bg-gradient-to-br from-green-100 via-green-200 to-green-300 min-h-screen flex items-center justify-center py-8">

<div class="flex flex-col md:flex-row w-full max-w-6xl rounded-3xl overflow-hidden shadow-2xl">

  {{-- ── Left Panel ───────────────────────────────────────────── --}}
  <div class="md:w-1/2 bg-gradient-to-tr from-green-700 to-green-800 text-white flex flex-col items-center justify-center p-12 relative">
    <div class="relative z-10 flex flex-col items-center text-center space-y-6">
      <div class="sticker"></div>
      <h1 class="text-5xl font-extrabold" style="text-shadow:2px 6px 15px rgba(0,0,0,0.4)">CRM FruitStand</h1>
      <p class="text-lg text-green-100 leading-relaxed">Effortlessly manage inventory, sales, and customers with a modern, intuitive interface.</p>

      {{-- Progress Steps --}}
      <div class="flex items-center space-x-2 mt-4">
        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-green-700 font-bold text-sm">1</div>
        <div class="w-12 h-1 bg-green-900 rounded"></div>
        <div class="w-8 h-8 rounded-full bg-green-900 flex items-center justify-center text-green-400 font-bold text-sm">2</div>
        <div class="w-12 h-1 bg-green-900 rounded"></div>
        <div class="w-8 h-8 rounded-full bg-green-900 flex items-center justify-center text-green-400 font-bold text-sm">3</div>
      </div>
    </div>
  </div>

  {{-- ── Right Panel ──────────────────────────────────────────── --}}
  <div class="md:w-1/2 flex items-center justify-center p-10 bg-white relative">
    <div class="w-full max-w-md glass p-10">
      <h2 class="text-3xl font-bold text-gray-700 mb-6 text-center">Welcome Back</h2>

      {{-- Rate-limit / lockout banner (email key used for this by the controller) --}}
      @if($errors->has('email') && str_contains($errors->first('email'), 'Too many'))
        <div class="alert-lockout mb-5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          </svg>
          <span>{{ $errors->first('email') }}</span>
        </div>
      @endif

      {{-- Success flash (e.g. password reset link sent) --}}
      @if(session('success'))
        <div class="alert-success mb-5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      <form action="{{ route('login') }}" method="POST" class="space-y-5">
        @csrf

        {{-- ── Email ───────────────────────────────────────── --}}
        <div>
          <div class="form-group">
            <input
              type="email"
              name="email"
              id="email"
              placeholder=" "
              required
              autocomplete="email"
              class="form-input {{ $errors->has('email') && !str_contains($errors->first('email'), 'Too many') ? 'input-error' : '' }}"
              value="{{ old('email') }}"
            >
            <label class="form-label" for="email">Email Address</label>
          </div>

          {{-- Email-specific error (not the lockout message) --}}
          @if($errors->has('email') && !str_contains($errors->first('email'), 'Too many'))
            <p class="field-error">
              {{-- Icon: envelope with X --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              {{ $errors->first('email') }}
            </p>
          @endif
        </div>

        {{-- ── Password ─────────────────────────────────────── --}}
        <div>
          <div class="form-group">
            <input
              type="password"
              name="password"
              id="password"
              placeholder=" "
              required
              autocomplete="current-password"
              class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
            >
            <label class="form-label" for="password">Password</label>

            {{-- Show / Hide toggle --}}
            <button
              type="button"
              class="toggle-password"
              onclick="togglePassword(this)"
              aria-label="Toggle password visibility"
              title="Show / hide password"
            >
              {{-- Eye (shown when password is hidden) --}}
              <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              {{-- Eye-off (shown when password is visible) --}}
              <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m1.344-1.892A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.966 9.966 0 01-1.879 3.236M15 12a3 3 0 00-4.243-2.758M3 3l18 18"/>
              </svg>
            </button>
          </div>

          {{-- Password-specific error --}}
          @if($errors->has('password'))
            <p class="field-error">
              {{-- Icon: lock --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              {{ $errors->first('password') }}
            </p>
          @endif
        </div>

        {{-- Forgot password --}}
        <div class="text-right -mt-1">
          <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-login">Login</button>
      </form>

      <p class="text-center text-gray-400 mt-6 text-sm">
        &copy; {{ date('Y') }} CRM FruitStand. All rights reserved.
      </p>
    </div>
  </div>

</div>

<script>
  function togglePassword(btn) {
    const input  = document.getElementById('password');
    const eyeOn  = document.getElementById('icon-eye');
    const eyeOff = document.getElementById('icon-eye-off');
    const show   = input.type === 'password';

    input.type = show ? 'text' : 'password';
    eyeOn.classList.toggle('hidden', show);
    eyeOff.classList.toggle('hidden', !show);

    // Keep focus on the input after toggle
    input.focus();
  }
</script>
</body>
</html>