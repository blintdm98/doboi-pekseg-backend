@use(App\Enums\OrderStatuses)
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comandă #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .order-info {
            margin-bottom: 30px;
        }
        .order-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-info td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .order-info td:first-child {
            font-weight: bold;
            width: 30%;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .products-table th,
        .products-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .products-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comandă</h1>
        <p>
            TVA: {{ config('app.tva_percentage') }}%
        </p>
    </div>

    @php
        $total = $order->orderDetails->sum(function($detail) {
            return ($detail->dispatched_quantity > 0 ? $detail->dispatched_quantity : $detail->quantity) * ($detail->product->price ?? 0);
        });
    @endphp

    <div class="order-info">
        <table>
            <tr>
                <td>Numărul comenzii:</td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td>Numele magazinului:</td>
                <td>{{ $order->store->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Telefon magazinului:</td>
                <td>{{ $order->store->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td>Numele clientului:</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Data:</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    @if(OrderStatuses::tryFrom($order->status))
                        {{OrderStatuses::tryFrom($order->status)->label()}}
                    @else
                        {{$order->status}}
                    @endif
                </td>
            </tr>
            @if($order->comment)
            <tr>
                <td>Comentariu:</td>
                <td>{{ $order->comment }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>Produs</th>
                <th>Cantitate</th>
                <th>Preț unitar</th>
                <th>Preț</th>
                <th>TVA</th>
                <th>TVA+Preț</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $detail)
                @if($detail->product)
                @php
                    $price = $detail->product->price;
                    $quantity = $detail->dispatched_quantity > 0 ? $detail->dispatched_quantity : $detail->quantity;
                    $subtotal = $price * $quantity;
                    $tvaAmount = $subtotal * (config('app.tva_percentage') / 100);
                    $totalWithTva = $subtotal + $tvaAmount;
                @endphp
                <tr>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $quantity }} buc</td>
                    <td>{{ number_format($price, 0, ',', ' ') }} lei</td>
                    <td>{{ number_format($subtotal, 0, ',', ' ') }} lei</td>
                    <td>{{ number_format($tvaAmount, 0, ',', ' ') }} lei</td>
                    <td>{{ number_format($totalWithTva, 0, ',', ' ') }} lei</td>
                </tr>
                @endif
            @endforeach
            @php
                $totalTva = $total * (config('app.tva_percentage') / 100);
                $totalWithTva = $total + $totalTva;
            @endphp
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong>{{ number_format($total, 0, ',', ' ') }} lei</strong></td>
                <td><strong>{{ number_format($totalTva, 0, ',', ' ') }} lei</strong></td>
                <td><strong>{{ number_format($totalWithTva, 0, ',', ' ') }} lei</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Document generat: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html> 