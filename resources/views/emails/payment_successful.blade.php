<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
</head>
<body>
    <h1>Thank you  for your payment!</h1>
    <p>Your payment of {{ $payment->amount }} {{ $payment->currency }} was successful.</p>
    <p>Order ID: {{ $payment->order_id }}</p>
    <p>We appreciate your business!</p>
</body>
</html>
