@php
    use Illuminate\Support\Arr;
    use App\Enums\OrderStatuses;
    use App\Helpers\GeneralHelper;
@endphp

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>{{ ($language ?? 'hu') === 'ro' ? 'Comenzi PDF' : 'Rendelések PDF' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>{{ ($language ?? 'hu') === 'ro' ? 'Comenzi' : 'Rendelések' }}</h2>
    @php
        $sumTotal = 0;
        $sumTva = 0;
        $sumTotalWithTva = 0;
    @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Magazin' : 'Bolt' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Stare' : 'Státusz' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Data' : 'Dátum' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Total' : 'Végösszeg' }}</th>
                <th>TVA</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Total cu TVA' : 'Végösszeg + ÁFA' }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            @php
                $total = $order->orderDetails->sum(function($detail) { return ($detail->dispatched_quantity > 0 ? $detail->dispatched_quantity : $detail->quantity) * ($detail->product->price ?? 0); });
                $tva = $total * 0.19;
                $totalWithTva = $total + $tva;
                $sumTotal += $total;
                $sumTva += $tva;
                $sumTotalWithTva += $totalWithTva;
            @endphp
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->store->name ?? (($language ?? 'hu') === 'ro' ? 'Magazin șters' : 'Törölt bolt') }}</td>
                <td>
                    @if(OrderStatuses::tryFrom($order->status))
                        {{OrderStatuses::tryFrom($order->status)->label()}}
                    @else
                        {{$order->status}}
                    @endif
                </td>
                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                <td>{{ GeneralHelper::displayPrice($total) }} </td>
                <td>{{ GeneralHelper::displayPrice($tva) }} </td>
                <td>{{ GeneralHelper::displayPrice($totalWithTva) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:left;font-weight:bold;">{{ ($language ?? 'hu') === 'ro' ? 'Total' : 'Összeg' }}</td>
                <td style="font-weight:bold;">{{ GeneralHelper::displayPrice($sumTotal) }}</td>
                <td style="font-weight:bold;">{{ GeneralHelper::displayPrice($sumTva) }}</td>
                <td style="font-weight:bold;">{{ GeneralHelper::displayPrice($sumTotalWithTva) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html> 