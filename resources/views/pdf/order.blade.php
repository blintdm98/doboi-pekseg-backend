@use(\App\Enums\OrderStatuses)
@use(App\Helpers\GeneralHelper)
<!DOCTYPE html>
<html lang="{{ $language ?? 'hu' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ ($language ?? 'hu') === 'ro' ? 'Comandă' : 'Rendelés' }} #{{ $order->id }}</title>
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
        <h1>{{ ($language ?? 'hu') === 'ro' ? 'Comandă' : 'Rendelés' }} #{{ $order->id }}</h1>
        <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
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
                <td>{{ ($language ?? 'hu') === 'ro' ? 'Numărul comenzii:' : 'Rendelés száma:' }}</td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td>{{ ($language ?? 'hu') === 'ro' ? 'Numele magazinului:' : 'Üzlet neve:' }}</td>
                <td>{{ $order->store->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>{{ ($language ?? 'hu') === 'ro' ? 'Telefon magazinului:' : 'Üzlet telefonszáma:' }}</td>
                <td>{{ $order->store->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ ($language ?? 'hu') === 'ro' ? 'Numele clientului:' : 'Rendelő neve:' }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>{{ ($language ?? 'hu') === 'ro' ? 'Data:' : 'Dátum:' }}</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td>{{ ($language ?? 'hu') === 'ro' ? 'Status:' : 'Státusz:' }}</td>
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
                <td>{{ ($language ?? 'hu') === 'ro' ? 'Comentariu:' : 'Megjegyzés:' }}</td>
                <td>{{ $order->comment }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Produs' : 'Termék' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Cantitate' : 'Mennyiség' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Preț unitar' : 'Egységár' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Preț' : 'Ár' }}</th>
                <th>TVA</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'TVA+Preț' : 'TVA+Ár' }}</th>
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
                    <td>
                        <div>
                            {{ $detail->product->name }}
                            @if($detail->product->accounting_code)
                                <br><span style="font-size: 11px; color: #666;">({{ $detail->product->accounting_code }})</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $quantity }} {{ ($language ?? 'hu') === 'ro' ? 'buc' : 'db' }}</td>
                    <td>{{ GeneralHelper::displayPrice($price ,0) }}</td>
                    <td>{{ GeneralHelper::displayPrice($subtotal, 0) }}</td>
                    <td>{{ number_format($tvaAmount, 0, ',', ' ') }} {{ ($language ?? 'hu') === 'ro' ? 'lei' : 'lej' }}</td>
                    <td>{{ number_format($totalWithTva, 0, ',', ' ') }} {{ ($language ?? 'hu') === 'ro' ? 'lei' : 'lej' }}</td>
                </tr>
                @endif
            @endforeach
            @php
                $totalTva = $total * (config('app.tva_percentage') / 100);
                $totalWithTva = $total + $totalTva;
            @endphp
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>{{ ($language ?? 'hu') === 'ro' ? 'Total:' : 'Összesen:' }}</strong></td>
                <td><strong>{{ number_format($total, 0, ',', ' ') }} {{ ($language ?? 'hu') === 'ro' ? 'lei' : 'lej' }}</strong></td>
                <td><strong>{{ number_format($totalTva, 0, ',', ' ') }} {{ ($language ?? 'hu') === 'ro' ? 'lei' : 'lej' }}</strong></td>
                <td><strong>{{ number_format($totalWithTva, 0, ',', ' ') }} {{ ($language ?? 'hu') === 'ro' ? 'lei' : 'lej' }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>{{ ($language ?? 'hu') === 'ro' ? 'Document generat:' : 'Dokumentum generálva:' }} {{ now()->format('Y-m-d H:i:s') }}</td>
    </div>
</body>
</html> 