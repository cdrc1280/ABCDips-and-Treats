<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email Verification Code — ABCDips & Treats</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #FBF3E7; color: #2D1B0E; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(92,58,34,0.12); }
        .header { background: linear-gradient(135deg, #5C3A22 0%, #8B5C35 100%); padding: 36px 32px; text-align: center; }
        .header img { width: 64px; height: 64px; border-radius: 16px; margin-bottom: 12px; }
        .header h1 { color: #FBF3E7; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { color: #D9A876; font-size: 13px; margin-top: 4px; }
        .body { padding: 36px 32px; }
        .greeting { font-size: 16px; font-weight: 600; color: #2D1B0E; margin-bottom: 12px; }
        .message { font-size: 14px; color: #5C4A3A; line-height: 1.6; margin-bottom: 28px; }
        .otp-box { background: linear-gradient(135deg, #FBF3E7 0%, #F5E6D0 100%); border: 2px dashed #C08E5D; border-radius: 16px; padding: 28px; text-align: center; margin-bottom: 28px; }
        .otp-label { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #8B6940; font-weight: 700; margin-bottom: 8px; }
        .otp-code { font-size: 48px; font-weight: 900; letter-spacing: 12px; color: #5C3A22; font-family: 'Courier New', monospace; line-height: 1; }
        .otp-expires { font-size: 12px; color: #8B6940; margin-top: 12px; }
        .warning-box { background: #FFF8E7; border-left: 4px solid #C98A3A; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #7A5C1A; }
        .footer { background: #F5EAD8; padding: 20px 32px; text-align: center; font-size: 12px; color: #8B6940; border-top: 1px solid #E8D5B5; }
        .footer strong { color: #5C3A22; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>ABCDips &amp; Treats</h1>
        <p>Email Verification</p>
    </div>
    <div class="body">
        <p class="greeting">Hello, {{ $recipientName }}!</p>
        <p class="message">
            You requested to verify your email address for your ABCDips & Treats account.
            Use the verification code below to complete your checkout. This code is valid for <strong>10 minutes</strong>.
        </p>

        <div class="otp-box">
            <p class="otp-label">Your Verification Code</p>
            <p class="otp-code">{{ $otp }}</p>
            <p class="otp-expires">Expires in 10 minutes</p>
        </div>

        <div class="warning-box">
            <strong>Did not request this?</strong> You can safely ignore this email. Your account remains secure.
        </div>

        <p class="message" style="font-size: 13px;">
            Enter this 6-digit code in the verification box on the checkout page to unlock your order.
        </p>
    </div>
    <div class="footer">
        <p>Sent with care by <strong>ABCDips &amp; Treats</strong></p>
        <p style="margin-top: 4px;">© {{ date('Y') }} ABCDips & Treats. All rights reserved.</p>
    </div>
</div>
</body>
</html>
