@use(\App\Enums\OrderStatuses)
@use(App\Helpers\GeneralHelper)
<!DOCTYPE html>
<html lang="{{ $language ?? 'hu' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ ($language ?? 'hu') === 'ro' ? 'Comanda' : 'Rendelés' }} #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
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
        .product-name {
            font-weight: 500;
        }
        .accounting-code {
            color: #666;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ($language ?? 'hu') === 'ro' ? 'Comanda' : 'Rendelés' }} #{{ $order->id }}</h1>
        <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
            {{ ($language ?? 'hu') === 'ro' ? 'Termeni și cantități' : 'Termékek és mennyiségek' }}
        </p>
    </div>

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
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Cantitate comandată' : 'Rendelt mennyiség' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Cantitate expediată' : 'Kiküldött mennyiség' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $detail)
                @if($detail->product)
                <tr>
                    <td>
                        <div class="product-name">
                            {{ $detail->product->name }}
                            @if($detail->product->accounting_code)
                                <span class="accounting-code">({{ $detail->product->accounting_code }})</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $detail->quantity }} {{ ($language ?? 'hu') === 'ro' ? 'buc' : 'db' }}</td>
                    <td>{{ $detail->dispatched_quantity }} {{ ($language ?? 'hu') === 'ro' ? 'buc' : 'db' }}</td>
                </tr>
                @endif
            @endforeach
            @php
                $totalQuantity = $order->orderDetails->sum('quantity');
                $totalDispatched = $order->orderDetails->sum('dispatched_quantity');
            @endphp
            <tr class="total-row">
                <td style="text-align: right;"><strong>{{ ($language ?? 'hu') === 'ro' ? 'Total:' : 'Összesen:' }}</strong></td>
                <td><strong>{{ $totalQuantity }} {{ ($language ?? 'hu') === 'ro' ? 'buc' : 'db' }}</strong></td>
                <td><strong>{{ $totalDispatched }} {{ ($language ?? 'hu') === 'ro' ? 'buc' : 'db' }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>{{ ($language ?? 'hu') === 'ro' ? 'Document generat:' : 'Dokumentum generálva:' }} {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
