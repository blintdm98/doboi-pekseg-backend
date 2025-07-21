<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rendelés #{{ $order->id }}</title>
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
        <h1>Rendelés #{{ $order->id }}</h1>
        <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
            TVA: {{ config('app.tva_percentage') }}%
        </p>
    </div>

    <div class="order-info">
        <table>
            <tr>
                <td>Rendelés száma:</td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td>Üzlet neve:</td>
                <td>{{ $order->store->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Üzlet telefonszáma:</td>
                <td>{{ $order->store->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td>Rendelő neve:</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Dátum:</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td>Státusz:</td>
                <td>
                    @switch($order->status)
                        @case('pending')
                            Függőben
                            @break
                        @case('partial')
                            Részben teljesítve
                            @break
                        @case('completed')
                            Teljesítve
                            @break
                        @case('canceled')
                            Visszamondva
                            @break
                        @default
                            {{ $order->status }}
                    @endswitch
                </td>
            </tr>
            @if($order->comment)
            <tr>
                <td>Megjegyzés:</td>
                <td>{{ $order->comment }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>Termék</th>
                <th>Mennyiség</th>
                <th>Egységár</th>
                <th>Ár</th>
                <th>TVA</th>
                <th>TVA+Ár</th>
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
                    <td>{{ $quantity }} db</td>
                    <td>{{ number_format($price, 0, ',', ' ') }} lej</td>
                    <td>{{ number_format($subtotal, 0, ',', ' ') }} lej</td>
                    <td>{{ number_format($tvaAmount, 0, ',', ' ') }} lej</td>
                    <td>{{ number_format($totalWithTva, 0, ',', ' ') }} lej</td>
                </tr>
                @endif
            @endforeach
            @php
                $totalTva = $total * (config('app.tva_percentage') / 100);
                $totalWithTva = $total + $totalTva;
            @endphp
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>Összesen:</strong></td>
                <td><strong>{{ number_format($total, 0, ',', ' ') }} lej</strong></td>
                <td><strong>{{ number_format($totalTva, 0, ',', ' ') }} lej</strong></td>
                <td><strong>{{ number_format($totalWithTva, 0, ',', ' ') }} lej</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumentum generálva: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html> 