<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 14px; }
        .header { font-size: 20px; font-weight: bold; margin-bottom: 20px; }
        .section { margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ccc; padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">INVOICE</div>
    <div class="section">Name: {{ $user_name }}</div>
    <div class="section">Amount: {{ $amount }} €</div>
    <div class="section">Date: {{ \Carbon\Carbon::now()->format('d.m.Y') }}</div>
    <div class="section">Invoice No: INV-{{ rand(1000,9999) }}</div>

    <table class="table">
        <tr>
            <th>Description</th>
            <th>Price</th>
        </tr>
        <tr>
            <td>Service</td>
            <td>{{ $amount }} €</td>
        </tr>
    </table>

    <div>Thank you for your payment!</div>
</body>
</html>