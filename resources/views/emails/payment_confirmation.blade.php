<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Notification</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 14px; color: #222; }
        .footer { margin-top: 40px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <!-- Pozdrav korisniku -->
    <p>Dear {{ $reservation->user_name }},</p>
    <!-- Poruka korisniku da su u prilogu dva dokumenta -->
    <p>Thank you for your payment. Attached to this email you will find two documents:</p>
    <ul>
        <li><strong>Invoice</strong></li>
        <li><strong>Payment Confirmation</strong></li>
    </ul>
    <p>Please keep them for your records.</p>
    <!-- Pozdrav od opštine -->
    <p>Best regards,<br>
    Municipality of Kotor</p>
    <!-- Futer sa napomenom o automatskoj poruci -->
    <div class="footer">
        This message was generated automatically {{ \Carbon\Carbon::now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>