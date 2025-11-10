@use(\App\Enums\OrderStatuses)
@use(App\Helpers\GeneralHelper)
<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('common.order') }} #{{ $order->id }}</title>
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
        <h1>{{ __('common.order') }} #{{ $order->id }}</h1>
        <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
            TVA: {{ config('app.tva_percentage') }}%
        </p>
    </div>

    @php
        $total = 0;
        $totalTvaByRate = [];
    @endphp

    <div class="order-info">
        <table>
            <tr>
                <td>{{ __('common.order_number') }}:</td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td>{{ __('common.store_name') }}:</td>
                <td>{{ $order->store->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>{{ __('common.store_phone') }}:</td>
                <td>{{ $order->store->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ __('common.customer_name') }}:</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>{{ __('common.date') }}:</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td>{{ __('common.status') }}:</td>
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
                <td>{{ __('common.comment') }}:</td>
                <td>{{ $order->comment }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>{{ __('common.product') }}</th>
                <th>{{ __('common.quantity') }}</th>
                <th>{{ __('common.unit_price') }}</th>
                <th>{{ __('common.price') }}</th>
                <th>TVA</th>
                <th>{{ __('common.price_with_tva') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $detail)
                @if($detail->product)
                @php
                    $price = $detail->price ?? $detail->product->price ?? 0;
                    $tvaRate = $detail->tva ?? $detail->product->tva ?? 11;
                    $quantity = $detail->dispatched_quantity > 0 ? $detail->dispatched_quantity : $detail->quantity;
                    $unitValue = $detail->unit_value ?? ($detail->product && $detail->product->unit === 'kg' ? ($detail->product->unit_value ?? 1) : 1);
                    $effectiveMultiplier = ($detail->product && $detail->product->unit === 'kg')
                        ? ($unitValue > 0 ? ($quantity / $unitValue) : 0)
                        : $quantity;
                    $subtotal = $price * $effectiveMultiplier;
                    $tvaAmount = $subtotal * ($tvaRate / 100);
                    $totalWithTva = $subtotal + $tvaAmount;

                    $total += $subtotal;
                    if (!isset($totalTvaByRate[$tvaRate])) {
                        $totalTvaByRate[$tvaRate] = 0;
                    }
                    $totalTvaByRate[$tvaRate] += $tvaAmount;
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
                    <td>{{ $quantity }} {{ __('common.pieces') }}</td>
                    <td>{{ GeneralHelper::displayPrice($price ,0) }}</td>
                    <td>{{ GeneralHelper::displayPrice($subtotal, 0) }}</td>
                    <td>{{ number_format($tvaAmount, 0, ',', ' ') }} {{ __('common.currency') }}</td>
                    <td>{{ number_format($totalWithTva, 0, ',', ' ') }} {{ __('common.currency') }}</td>
                </tr>
                @endif
            @endforeach
            @php
                $totalTva = array_sum($totalTvaByRate);
                $grandTotal = $total + $totalTva;
            @endphp
            @if(count($totalTvaByRate) > 1)
                @foreach($totalTvaByRate as $rate => $tvaSum)
                <tr style="background-color: #fafafa;">
                    <td colspan="3" style="text-align: right;">{{ __('common.tva') }} {{ $rate }}%:</td>
                    <td></td>
                    <td>{{ number_format($tvaSum, 0, ',', ' ') }} {{ __('common.currency') }}</td>
                    <td></td>
                </tr>
                @endforeach
            @endif
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>{{ __('common.total') }}:</strong></td>
                <td><strong>{{ number_format($total, 0, ',', ' ') }} {{ __('common.currency') }}</strong></td>
                <td><strong>{{ number_format($totalTva, 0, ',', ' ') }} {{ __('common.currency') }}</strong></td>
                <td><strong>{{ number_format($grandTotal, 0, ',', ' ') }} {{ __('common.currency') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>{{ __('common.document_generated') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html> 