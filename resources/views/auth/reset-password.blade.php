<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - CRM FruitStand</title>
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
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.15), transparent 70%);
      pointer-events: none;
  }
  .form-group { position: relative; }
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
  .form-label {
      position: absolute;
      left: 1rem;
      top: 1rem;
      color: #6b7280;
      font-size: 1rem;
      pointer-events: none;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.85);
      padding: 0 0.25rem;
  }
  .form-input:focus + .form-label,
  .form-input:not(:placeholder-shown) + .form-label {
      top: -0.5rem;
      font-size: 0.75rem;
      color: #10b981;
  }
  .toggle-pw {
      position: absolute;
      right: 0.85rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #9ca3af;
      padding: 0;
      display: flex;
      align-items: center;
      transition: color 0.2s ease;
  }
  .toggle-pw:hover { color: #10b981; }
  .sticker {
      width: 130px;
      height: 130px;
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
      top: 10%;
      left: 10%;
      width: 80%;
      height: 80%;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.4), transparent 60%);
      pointer-events: none;
  }
  @keyframes float {
      0% { transform: translateY(0) rotate(-2deg); }
      100% { transform: translateY(-10px) rotate(3deg); }
  }
  .btn-submit {
      background: linear-gradient(135deg, #064630ff, #02130dff);
      color: white;
      font-weight: 600;
      padding: 0.85rem;
      border-radius: 0.75rem;
      width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15), 0 4px 10px rgba(0,0,0,0.08) inset;
  }
  .btn-submit:hover {
      background: linear-gradient(135deg, #112e25ff, #14523dff);
      box-shadow: 0 15px 35px rgba(0,0,0,0.25), 0 4px 10px rgba(255,255,255,0.15) inset;
      transform: translateY(-2px);
  }
  .btn-submit:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
  }
  .back-link {
      color: #059669;
      text-decoration: none;
      font-size: 0.875rem;
      transition: all 0.3s ease;
      font-weight: 500;
  }
  .back-link:hover {
      color: #047857;
      text-decoration: underline;
  }
  .left-panel h1 {
      font-size: 3.2rem;
      font-weight: 800;
      color: white;
      text-shadow: 2px 6px 15px rgba(0,0,0,0.4);
  }
  .left-panel p {
      font-size: 1.125rem;
      color: #d1fae5;
      line-height: 1.7;
  }
  .left-panel::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(0,0,0,0.25), rgba(0,0,0,0.05));
      z-index: 0;
  }
  .requirement {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.75rem;
      color: #9ca3af;
      transition: color 0.3s ease;
  }
  .requirement.met { color: #10b981; }
  .requirement .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #d1d5db;
      transition: background 0.3s ease;
      flex-shrink: 0;
  }
  .requirement.met .dot { background: #10b981; }
  .strength-bar {
      height: 4px;
      border-radius: 2px;
      background: #e5e7eb;
      overflow: hidden;
  }
  .strength-fill {
      height: 100%;
      border-radius: 2px;
      transition: width 0.4s ease, background 0.4s ease;
      width: 0%;
  }
  @media (max-width: 768px) {
      .left-panel, .right-panel { width: 100%; }
      .sticker { width: 110px; height: 110px; }
      .glass { padding: 3rem 2rem; }
  }
</style>
</head>
<body class="bg-gradient-to-br from-green-100 via-green-200 to-green-300 min-h-screen flex items-center justify-center py-8">

<div class="flex flex-col md:flex-row w-full max-w-6xl rounded-3xl overflow-hidden shadow-2xl">

  <!-- Left Panel -->
  <div class="md:w-1/2 left-panel bg-gradient-to-tr from-green-700 to-green-800 text-white flex flex-col items-center justify-center p-12 relative">
    <div class="relative z-10 flex flex-col items-center text-center space-y-6">
      <div class="sticker"></div>
      <h1>CRM FruitStand</h1>
      <p>Create a strong new password to secure your account.</p>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="md:w-1/2 right-panel flex items-center justify-center p-10 bg-white relative">
    <div class="w-full max-w-md glass p-10">
      <h2 class="text-3xl font-bold text-gray-700 mb-2 text-center">Reset Password</h2>
      <p class="text-center text-gray-500 mb-6 text-sm">Enter the code sent to your email</p>

      @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center font-medium">
            {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('password.update') }}" method="POST" class="space-y-4" id="resetForm">
        @csrf
        <input type="hidden" name="email" value="{{ request('email') }}">

        <!-- Reset Code -->
        <div class="form-group">
          <input type="text" name="code" placeholder=" " required class="form-input" maxlength="6">
          <label class="form-label">Reset Code</label>
        </div>

        <!-- New Password -->
        <div class="form-group">
          <input
            type="password"
            name="password"
            id="password"
            placeholder=" "
            required
            class="form-input"
            minlength="12"
            maxlength="16"
            oninput="checkStrength(this.value)"
          >
          <label class="form-label">New Password</label>
          <button type="button" class="toggle-pw" onclick="togglePassword('password', 'eyeIcon1')">
            <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
              viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                   -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>

        <!-- Strength Bar -->
        <div class="strength-bar">
          <div class="strength-fill" id="strengthFill"></div>
        </div>

        <!-- Requirements Checklist -->
        <div class="space-y-1 px-1">
          <div class="requirement" id="req-length">
            <span class="dot"></span> 12 to 16 characters
          </div>
          <div class="requirement" id="req-upper">
            <span class="dot"></span> At least one uppercase letter (A-Z)
          </div>
          <div class="requirement" id="req-lower">
            <span class="dot"></span> At least one lowercase letter (a-z)
          </div>
          <div class="requirement" id="req-number">
            <span class="dot"></span> At least one number (0-9)
          </div>
          <div class="requirement" id="req-special">
            <span class="dot"></span> At least one special character (!@#$%^&*)
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            placeholder=" "
            required
            class="form-input"
            minlength="12"
            maxlength="16"
            oninput="checkMatch()"
          >
          <label class="form-label">Confirm Password</label>
          <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', 'eyeIcon2')">
            <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
              viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                   -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>

        <!-- Match Message -->
        <p id="matchMsg" class="text-xs px-1 hidden"></p>

        <button type="submit" class="btn-submit" id="submitBtn" disabled>
          Reset Password
        </button>
      </form>

      <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
      </div>

      <p class="text-center text-gray-400 mt-6 text-sm">
        &copy; {{ date('Y') }} CRM FruitStand. All rights reserved.
      </p>
    </div>
  </div>

</div>

<script>
  let passwordValid = false;
  let passwordsMatch = false;

  // ── Show/Hide Password ────────────────────────────────────
  function togglePassword(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon  = document.getElementById(iconId);
    const isHidden = field.type === 'password';

    field.type = isHidden ? 'text' : 'password';

    // Swap between eye and eye-slash SVG
    icon.innerHTML = isHidden
      ? // Eye-slash (hide)
        `<path stroke-linecap="round" stroke-linejoin="round"
          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
             a9.956 9.956 0 012.293-3.95M6.696 6.696A9.953 9.953 0 0112 5
             c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.138 5.169
             M3 3l18 18" />`
      : // Eye (show)
        `<path stroke-linecap="round" stroke-linejoin="round"
          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
         <path stroke-linecap="round" stroke-linejoin="round"
          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
             -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
  }

  // ── Password Strength ─────────────────────────────────────
  function checkStrength(value) {
    const reqs = {
      'req-length':  value.length >= 12 && value.length <= 16,
      'req-upper':   /[A-Z]/.test(value),
      'req-lower':   /[a-z]/.test(value),
      'req-number':  /[0-9]/.test(value),
      'req-special': /[!@#$%^&*()\-_=+\[\]{};':"\\|,.<>\/?]/.test(value),
    };

    let metCount = 0;
    for (const [id, met] of Object.entries(reqs)) {
      const el = document.getElementById(id);
      el.classList.toggle('met', met);
      if (met) metCount++;
    }

    const fill = document.getElementById('strengthFill');
    fill.style.width = (metCount / 5 * 100) + '%';

    if (metCount <= 2)      fill.style.background = '#ef4444';
    else if (metCount <= 3) fill.style.background = '#f59e0b';
    else if (metCount <= 4) fill.style.background = '#3b82f6';
    else                    fill.style.background = '#10b981';

    passwordValid = metCount === 5;
    updateSubmit();
    checkMatch();
  }

  // ── Password Match ────────────────────────────────────────
  function checkMatch() {
    const pw  = document.getElementById('password').value;
    const cpw = document.getElementById('password_confirmation').value;
    const msg = document.getElementById('matchMsg');

    if (cpw.length === 0) {
      msg.classList.add('hidden');
      passwordsMatch = false;
    } else if (pw === cpw) {
      msg.textContent = '✓ Passwords match';
      msg.className = 'text-xs px-1 text-green-600';
      passwordsMatch = true;
    } else {
      msg.textContent = '✗ Passwords do not match';
      msg.className = 'text-xs px-1 text-red-500';
      passwordsMatch = false;
    }
    updateSubmit();
  }

  // ── Enable/Disable Submit ─────────────────────────────────
  function updateSubmit() {
    document.getElementById('submitBtn').disabled = !(passwordValid && passwordsMatch);
  }
</script>

</body>
</html>