<?php
namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalOrders;
    public $totalProductsOrdered;
    public $totalRevenue;
    public $topStoreName;
    public $topProducts = [];
    public $chartLabels = [];
    public $chartData = [];

    public function mount()
    {
        $this->totalOrders = Order::count();
        $this->totalProductsOrdered = OrderDetail::sum('quantity');
        $this->totalRevenue = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(order_details.quantity * products.price) as total'))
            ->value('total');

        $this->topStoreName = Order::select('store_id', DB::raw('COUNT(*) as total'))
            ->groupBy('store_id')
            ->orderByDesc('total')
            ->with('store')
            ->first()
            ?->store?->name ?? '–';

            $this->topProducts = OrderDetail::select('product_id', DB::raw('SUM(quantity) as total'))
                ->groupBy('product_id')
                ->orderByDesc('total')
                ->with('product')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'name' => $item->product->name ?? 'Törölt termék',
                        'total' => $item->total,
                    ];
                });

                $orders = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->chartLabels = $orders->pluck('date')->toArray();
        $this->chartData = $orders->pluck('count')->toArray();

    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard');
    }
}
