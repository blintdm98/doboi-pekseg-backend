<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Comandă #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
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
            padding: 5px 10px;
            border: 1px solid #ddd;
        }
        .order-info td:first-child {
            font-weight: bold;
            background-color: #f5f5f5;
            width: 30%;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .products-table th {
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            font-weight: bold;
        }
        .products-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comandă #{{ $order->id }}</h1>
        <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
            TVA: {{ config('app.tva_percentage') }}%
        </p>
    </div>

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
                    @switch($order->status)
                        @case('pending')
                            În așteptare
                            @break
                        @case('partial')
                            Parțial finalizată
                            @break
                        @case('completed')
                            Finalizată
                            @break
                        @case('canceled')
                            Anulată
                            @break
                        @default
                            {{ $order->status }}
                    @endswitch
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