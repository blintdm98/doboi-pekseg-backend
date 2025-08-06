<?php
namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Models\Product;
use App\Enums\OrderStatuses;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $chartLabels = [];
    public $chartData = [];

    // Szűrők
    public $dateStart = null;
    public $dateEnd = null;
    public $storeFilter = '';
    public $userFilter = '';
    public $productFilter = '';

    public function mount()
    {
        // Alapértelmezett egy heti szűrés
        $this->dateStart = Carbon::now()->subWeek()->format('Y-m-d');
        $this->dateEnd = Carbon::now()->format('Y-m-d');
        
        $this->loadChartData();
    }

    public function loadChartData()
    {
        // Debug: Logoljuk a szűrő értékeket
        \Log::info('Dashboard Filters', [
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd,
            'storeFilter' => $this->storeFilter,
            'userFilter' => $this->userFilter,
            'productFilter' => $this->productFilter,
        ]);

        // Grafikon adatok szűrőkkel - csak teljesített és részben teljesített rendelések
        $chartQuery = Order::query()
            ->whereIn('status', [OrderStatuses::COMPLETED, OrderStatuses::PARTIAL]);
        
        if (!empty($this->storeFilter)) {
            $chartQuery->where('store_id', $this->storeFilter);
        }
        if (!empty($this->userFilter)) {
            $chartQuery->where('user_id', $this->userFilter);
        }
        if (!empty($this->productFilter)) {
            $chartQuery->whereHas('orderDetails', function ($q) {
                $q->where('product_id', $this->productFilter);
            });
        }
        

        if (!empty($this->dateStart)) {
            $chartQuery->whereDate('created_at', '>=', $this->dateStart);
        }
        if (!empty($this->dateEnd)) {
            $chartQuery->whereDate('created_at', '<=', $this->dateEnd);
        }

        $orders = $chartQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->chartLabels = $orders->pluck('date')->toArray();
        $this->chartData = $orders->pluck('count')->toArray();

        \Log::info('Chart data updated.', [
            'labels' => $this->chartLabels,
            'data' => $this->chartData,
            'total_orders' => $orders->sum('count')
        ]);

        $this->dispatch('chartDataUpdated', [
            'labels' => $this->chartLabels,
            'data' => $this->chartData
        ]);
    }

    public function updatedDateStart($value)
    {
        \Log::info('DateStart Updated', ['value' => $value]);
        $this->loadChartData();
    }

    public function updatedDateEnd($value)
    {
        \Log::info('DateEnd Updated', ['value' => $value]);
        $this->loadChartData();
    }

    public function updatedStoreFilter($value)
    {
        \Log::info('StoreFilter Updated', ['value' => $value]);
        $this->loadChartData();
    }

    public function updatedUserFilter($value)
    {
        \Log::info('UserFilter Updated', ['value' => $value]);
        $this->loadChartData();
    }

    public function updatedProductFilter($value)
    {
        \Log::info('ProductFilter Updated', ['value' => $value]);
        $this->loadChartData();
    }

    public function getStores()
    {
        return Store::orderBy('name')->get();
    }

    public function getUsers()
    {
        return User::orderBy('name')->get();
    }

    public function getProducts()
    {
        return Product::orderBy('name')->get();
    }

    public function getChartTitle()
    {
        $filters = [];
        
        // Üzlet szűrő
        if (!empty($this->storeFilter)) {
            $store = Store::find($this->storeFilter);
            if ($store) {
                $filters[] = $store->name . ' ' . __('common.store');
            }
        }
        
        // Felhasználó szűrő
        if (!empty($this->userFilter)) {
            $user = User::find($this->userFilter);
            if ($user) {
                $filters[] = $user->name . ' ' . __('common.user');
            }
        }
        
        // Termék szűrő
        if (!empty($this->productFilter)) {
            $product = Product::find($this->productFilter);
            if ($product) {
                $filters[] = $product->name . ' ' . __('common.product');
            }
        }
        
        // Ha nincsenek szűrők, alapértelmezett cím
        if (empty($filters)) {
            return __('common.orders_timeline');
        }
        
        return implode(', ', $filters) . ' ' . __('common.orders');
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard', [
            'stores' => $this->getStores(),
            'users' => $this->getUsers(),
            'products' => $this->getProducts(),
        ]);
    }
}
