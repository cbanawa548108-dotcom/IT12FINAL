<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Blocked</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            border-top: 6px solid #ef4444;
        }
        .icon { font-size: 64px; margin-bottom: 16px; }
        h1 { color: #ef4444; font-size: 24px; margin-bottom: 12px; }
        p { color: #6b7280; font-size: 15px; line-height: 1.6; margin-bottom: 8px; }
        .until {
            display: inline-block;
            margin-top: 16px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .permanent {
            display: inline-block;
            margin-top: 16px;
            background: #f9fafb;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🚫</div>
        <h1>Access Denied</h1>
        <p>{{ $reason }}</p>
        @if($blocked_until)
            <div class="until">
                Block expires: {{ \Carbon\Carbon::parse($blocked_until)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
            </div>
        @else
            <div class="permanent">
                This block is permanent. Please contact an administrator.
            </div>
        @endif
    </div>
</body>
</html>