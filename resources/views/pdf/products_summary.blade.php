<!DOCTYPE html>
<html lang="{{ $language ?? 'hu' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($language ?? 'hu') === 'ro' ? 'Sumar Produse' : 'Termékek Összesítés' }}</title>
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
        .filters-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .filters-info p {
            margin: 2px 0;
            font-size: 11px;
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
        <h1>{{ ($language ?? 'hu') === 'ro' ? 'Sumar Produse' : 'Termékek Összesítés' }}</h1>
        <p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
            @if(!empty($filters['store']) && !empty($filters['dateStart']) && !empty($filters['dateEnd']) && $filters['dateStart'] === $filters['dateEnd'])
                {{ ($language ?? 'hu') === 'ro' ? 'Generat din comanda' : 'Rendelésből generálva' }} #{{ $filters['order_id'] ?? '' }}
            @else
                {{ ($language ?? 'hu') === 'ro' ? 'Generat din comenzi filtrate' : 'Szűrt rendelésekből generálva' }}
            @endif
        </p>
    </div>

    @if(!empty($filters['search']) || !empty($filters['status']) || !empty($filters['store']) || !empty($filters['user']) || !empty($filters['dateStart']) || !empty($filters['dateEnd']))
    <div class="filters-info">
        <h3 style="margin-top: 0; margin-bottom: 10px;">{{ ($language ?? 'hu') === 'ro' ? 'Filtre aplicate:' : 'Alkalmazott szűrők:' }}</h3>
        @if(!empty($filters['search']))
            <p><strong>{{ ($language ?? 'hu') === 'ro' ? 'Căutare:' : 'Keresés:' }}</strong> {{ $filters['search'] }}</p>
        @endif
        @if(!empty($filters['status']))
            <p><strong>{{ ($language ?? 'hu') === 'ro' ? 'Status:' : 'Státusz:' }}</strong> {{ $filters['status'] }}</p>
        @endif
        @if(!empty($filters['store']))
            <p><strong>{{ ($language ?? 'hu') === 'ro' ? 'Magazin:' : 'Üzlet:' }}</strong> {{ $filters['store'] }}</p>
        @endif
        @if(!empty($filters['user']))
            <p><strong>{{ ($language ?? 'hu') === 'ro' ? 'Utilizator:' : 'Felhasználó:' }}</strong> {{ $filters['user'] }}</p>
        @endif
        @if(!empty($filters['dateStart']))
            <p><strong>{{ ($language ?? 'hu') === 'ro' ? 'Data de la:' : 'Dátumtól:' }}</strong> {{ $filters['dateStart'] }}</p>
        @endif
        @if(!empty($filters['dateEnd']))
            <p><strong>{{ ($language ?? 'hu') === 'ro' ? 'Data până la:' : 'Dátumig:' }}</strong> {{ $filters['dateEnd'] }}</p>
        @endif
    </div>
    @endif

    <table class="products-table">
        <thead>
            <tr>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Produs' : 'Termék' }}</th>
                <th>{{ ($language ?? 'hu') === 'ro' ? 'Cantitate' : 'Mennyiség' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>
                    <div class="product-name">
                        {{ $product['name'] }}
                        @if($product['accounting_code'])
                            <span class="accounting-code">({{ $product['accounting_code'] }})</span>
                        @endif
                    </div>
                </td>
                <td><strong>{{ number_format($product['total_quantity'], 0, ',', ' ') }}
                    {{ $product['unit_label'] ?? (($language ?? 'hu') === 'ro' ? 'buc' : 'db') }}</strong></td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td style="text-align: right;"><strong>{{ ($language ?? 'hu') === 'ro' ? 'Total produse:' : 'Összes termék:' }}</strong></td>
                <td><strong>{{ count($products) }} {{ ($language ?? 'hu') === 'ro' ? 'tipuri' : 'fajta' }}</strong></td>
            </tr>
            <tr class="total-row">
                <td style="text-align: right;"><strong>{{ ($language ?? 'hu') === 'ro' ? 'Total cantitate:' : 'Összes mennyiség:' }}</strong></td>
                <td>
                    @php
                        $totalByUnit = collect($products)->groupBy('unit_label')->map(function($items) {
                            return [
                                'unit' => $items->first()['unit_label'] ?? __('common.unit_db'),
                                'sum' => $items->sum('total_quantity'),
                            ];
                        });
                    @endphp
                    @foreach($totalByUnit as $unit => $data)
                        <strong>{{ number_format($data['sum'], 0, ',', ' ') }} {{ $unit }}</strong>@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>{{ ($language ?? 'hu') === 'ro' ? 'Document generat:' : 'Dokumentum generálva:' }} {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

