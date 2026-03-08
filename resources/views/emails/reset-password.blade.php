<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #064630ff, #02130dff);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .code-box {
            background: #f0fdf4;
            border: 2px solid #10b981;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #064630ff;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #064630ff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍎 CRM FruitStand</h1>
            <p>Password Reset Request</p>
        </div>
        
        <div class="content">
            <h2>Reset Your Password</h2>
            <p>Hello,</p>
            <p>We received a request to reset your password. Use the code below to complete the process:</p>
            
            <div class="code-box">
                <p style="margin: 0; font-size: 14px; color: #6b7280;">Your Reset Code</p>
                <div class="code">{{ $code }}</div>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #6b7280;">Valid for 30 minutes</p>
            </div>
            
            <p>Enter this code on the password reset page to create a new password.</p>
            
            <div class="warning">
                <strong>⚠️ Security Notice:</strong> If you didn't request this password reset, please ignore this email or contact support if you're concerned about your account security.
            </div>
            
            <p>This code will expire in 30 minutes for security reasons.</p>
            
            <p>Best regards,<br>
            <strong>CRM FruitStand Team</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} CRM FruitStand. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>