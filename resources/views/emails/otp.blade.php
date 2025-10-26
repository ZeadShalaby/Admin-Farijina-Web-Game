<!-- resources/views/emails/otp.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #6c5ce7 0%, #a363d9 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 40px 20px;
            text-align: center;
        }

        .otp-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border: 2px dashed #6c5ce7;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #6c5ce7;
            margin: 10px 0;
            font-family: monospace;
        }

        .info {
            background: #fff3cd;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-left: 4px solid #ffc107;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .warning {
            color: #856404;
            font-size: 14px;
            margin-top: 20px;
        }

        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                width: auto;
            }

            .header h1 {
                font-size: 24px;
            }

            .otp-code {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p>Verification Code</p>
        </div>

        <div class="content">
            <h2>Hello!</h2>
            <p>You have requested a verification code for your account.</p>

            <div class="otp-box">
                <p>Your verification code is:</p>
                <div class="otp-code">{{ $otp }}</div>
                <p>This code will expire in {{ $validityMinutes }} minutes.</p>
            </div>

            <div class="info">
                <p><strong>Important:</strong></p>
                <ul>
                    <li>This code was requested for phone number: {{ $phone }}</li>
                    <li>If you didn't request this code, please ignore this email</li>
                    <li>Do not share this code with anyone</li>
                </ul>
            </div>

            <p class="warning">
                For security reasons, this verification code will expire in {{ $validityMinutes }} minutes.
            </p>
        </div>

        <div class="footer">
            <p>This is an automated message, please do not reply.</p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
