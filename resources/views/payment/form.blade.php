<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Card Payment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Card Payment</h2>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('payment.redirect-hpp') }}">
    @csrf
    <label>Amount (EUR):</label>
    <input type="number" name="amount" step="0.01" required>
    <label>Email:</label>
    <input type="email" name="email" required>
    <button type="submit">Proceed to Payment</button>
</form>
</body>
</html>