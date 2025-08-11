<?php

namespace App\Livewire\Admin\Order;

use App\Enums\OrderStatuses;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderList extends Component
{
    use WithPagination, WireUiActions;

    public $orderModal = false;

    public $selectedOrder;
    public $orderDetails = [];
    public $search = '';
    public $statusFilter = '';
    public $storeFilter = '';
    public $userFilter = '';
    public $dateStart = null;
    public $dateEnd = null;
    public $newProductId = null;
    public $newProductQuantity = 1;
    public bool $showAddProduct = false;
    public $existingProductIds = [];
    public $availableProducts = [];

    public function getTotalProperty()
    {
        if (!$this->selectedOrder) {
            return 0;
        }

        return $this->selectedOrder->orderDetails->sum(function ($detail) {
            $quantity = $detail->dispatched_quantity > 0 ? $detail->dispatched_quantity : $detail->quantity;
            return $quantity * ($detail->product->price ?? 0);
        });
    }

    public function getOrders()
    {
        $query = Order::query()
            ->with(['user', 'store', 'orderDetails', 'orderDetails.product'])
            ->search($this->search)
            ->filterStatus($this->statusFilter);


        if (!empty($this->storeFilter)) {
            $query->where('store_id', $this->storeFilter);
        }

        if (!empty($this->userFilter)) {
            $query->where('user_id', $this->userFilter);
        }

        if (!empty($this->dateStart)) {
            $query->whereDate('created_at', '>=', $this->dateStart);
        }

        if (!empty($this->dateEnd)) {
            $query->whereDate('created_at', '<=', $this->dateEnd);
        }
        return $query->latest()->paginate(50);
    }

    public function showOrder(Order $order)
    {
        $this->selectedOrder = $order;
        $this->orderDetails = OrderDetail::with('product')
            ->where('order_id', $order->id)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'product_name' => $item->product?->name ?? 'Ez a termék már nem elérhető',
                'quantity' => $item->quantity,
                'dispatched_quantity' => (($order->status === OrderStatuses::PENDING->value || $order->status === OrderStatuses::RETURNED->value) && $item->dispatched_quantity === 0)
                    ? $item->quantity
                    : $item->dispatched_quantity,
            ])
            ->toArray();

        // Csak akkor töltjük be a rendelkezésre álló termékeket, ha a rendelés nincs teljesítve
        if ($order->status !== OrderStatuses::COMPLETED->value) {
            $existingProductIds = OrderDetail::where('order_id', $order->id)->pluck('product_id')->toArray();
            $this->availableProducts = \App\Models\Product::whereNotIn('id', $existingProductIds)->orderBy('name')->get();
        } else {
            $this->availableProducts = collect();
        }

        // Reset the showAddProduct flag when opening a new order
        $this->showAddProduct = false;

        $this->orderModal = true;
    }

    public function save()
    {
        $isComplete = true;

        foreach ($this->orderDetails as $detail) {
            $dispatched = $detail['dispatched_quantity'] ?? 0;
            $ordered = $detail['quantity'] ?? 0;

            if ($dispatched < $ordered) {
                $isComplete = false;
            }

            OrderDetail::where('id', $detail['id'])->update([
                'dispatched_quantity' => $dispatched,
            ]);
        }

        if ($this->selectedOrder) {
            $this->selectedOrder->update([
                'status' => $isComplete ? OrderStatuses::COMPLETED->value : OrderStatuses::PARTIAL->value ,
            ]);

            // Ha a rendelés teljesítve lett, kikapcsoljuk a termék hozzáadás felületet
            if ($isComplete) {
                $this->showAddProduct = false;
                $this->availableProducts = collect();
            }
        }

        // Ne zárjuk be a modalt, csak frissítsük az adatokat
        // $this->orderModal = false;

        $this->notification()->send([
            'title' => __('common.saved_successfully'),
            'icon' => 'success',
        ]);
    }

    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->notification()->send([
                'title' => __('common.error'),
                'description' => __('common.ordernotfound'),
                'icon' => 'error',
            ]);
            return;
        }

        // Fizikai törlés helyett csak a státuszt állítjuk "Visszavonva" értékre
        // OrderDetail::where('order_id', $orderId)->delete();
        // $order->delete();

        // A rendelés státuszát "canceled" értékre állítjuk
        $order->update(['status' => OrderStatuses::CANCELED->value]);

        $this->orderModal = false;
        $this->selectedOrder = null;
        $this->orderDetails = [];

        $this->notification()->send([
            'title' => 'Rendelés visszavonva',
            'icon' => 'success',
        ]);
    }

    public function permanentlyDeleteOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->notification()->send([
                'title' => __('common.error'),
                'description' => __('common.ordernotfound'),
                'icon' => 'error',
            ]);
            return;
        }

        // Fizikai törlés
        OrderDetail::where('order_id', $orderId)->delete();
        $order->delete();

        $this->notification()->send([
            'title' => __('common.deleted_successfully'),
            'icon' => 'success',
        ]);
    }

    public function markAsPending($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->notification()->send([
                'title' => __('common.error'),
                'description' => __('common.ordernotfound'),
                'icon' => 'error',
            ]);
            return;
        }

        // Visszaküldött rendelés átállítása függőben státuszra
        $order->update(['status' => OrderStatuses::PENDING->value]);

        $this->orderModal = false;
        $this->selectedOrder = null;
        $this->orderDetails = [];

        $this->notification()->send([
            'title' => 'Rendelés átállítva függőben státuszra',
            'icon' => 'success',
        ]);
    }

    public function confirmReturn($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->notification()->send([
                'title' => __('common.error'),
                'description' => __('common.ordernotfound'),
                'icon' => 'error',
            ]);
            return;
        }

        // Visszaküldött rendelés megerősítése - marad RETURNED státuszban, de nem módosítható
        $order->update(['confirmed_return' => true]);

        $this->orderModal = false;
        $this->selectedOrder = null;
        $this->orderDetails = [];

        $this->notification()->send([
            'title' => 'Visszaküldött rendelés megerősítve',
            'icon' => 'success',
        ]);
    }

    public function addProductToOrder()
    {
        if (!$this->selectedOrder || !$this->newProductId || $this->newProductQuantity < 1) {
            return;
        }

        $product = \App\Models\Product::find($this->newProductId);

        if (!$product) {
            return;
        }

        if (OrderDetail::where('order_id', $this->selectedOrder->id)
            ->where('product_id', $product->id)
            ->exists()) {
            $this->notification()->send([
                'title' => 'Ez a termék már hozzá van adva a rendeléshez.',
                'icon' => 'warning',
            ]);
            return;
        }

        $detail = OrderDetail::create([
            'order_id' => $this->selectedOrder->id,
            'product_id' => $product->id,
            'quantity' => $this->newProductQuantity,
            'dispatched_quantity' => $this->newProductQuantity,
        ]);

        $this->orderDetails[] = [
            'id' => $detail->id,
            'product_name' => $product->name,
            'quantity' => $detail->quantity,
            'dispatched_quantity' => $detail->dispatched_quantity,
        ];

        $this->newProductId = null;
        $this->newProductQuantity = 1;

        $this->notification()->send([
            'title' => 'Termék hozzáadva a rendeléshez',
            'icon' => 'success',
        ]);

        $this->availableProducts = \App\Models\Product::whereNotIn(
            'id',
            OrderDetail::where('order_id', $this->selectedOrder->id)->pluck('product_id')->toArray()
        )->orderBy('name')->get();

        // Frissítjük a selectedOrder-t, hogy a total automatikusan frissüljön
        $this->selectedOrder = $this->selectedOrder->fresh(['orderDetails.product']);
    }

    public function updatedOrderDetails($value, $key)
    {
        // Frissítjük a selectedOrder-t, amikor a dispatched_quantity változik
        if (str_contains($key, 'dispatched_quantity')) {
            $this->selectedOrder = $this->selectedOrder->fresh(['orderDetails.product']);
        }
    }

    public function generatePDF($orderId, $language = 'hu')
    {
        if ($orderId) {
            $order = Order::with(['orderDetails.product', 'store', 'user'])->find($orderId);
            if (!$order) {
                $this->notification()->send([
                    'title' => 'Hiba',
                    'description' => 'A rendelés nem található.',
                    'icon' => 'error',
                ]);
                return;
            }
            $orders = collect([$order]);
        } else {
            // Szűrt rendelések lekérése
            $query = Order::with(['orderDetails.product', 'store', 'user'])
                ->search($this->search);

            if (!empty($this->statusFilter)) {
                $query->filterStatus($this->statusFilter);
            } else {
                // Csak a teljesített vagy részben teljesített rendelések, ha nincs státusz szűrő
                $query->whereIn('status', [OrderStatuses::COMPLETED->value , OrderStatuses::PARTIAL->value ]);
            }
            if (!empty($this->storeFilter)) {
                $query->where('store_id', $this->storeFilter);
            }
            if (!empty($this->userFilter)) {
                $query->where('user_id', $this->userFilter);
            }
            if (!empty($this->dateStart)) {
                $query->whereDate('created_at', '>=', $this->dateStart);
            }
            if (!empty($this->dateEnd)) {
                $query->whereDate('created_at', '<=', $this->dateEnd);
            }
            $orders = $query->get();
            if ($orders->isEmpty()) {
                $this->notification()->send([
                    'title' => 'Nincs találat',
                    'description' => 'Nincs rendelés a szűrési feltételeknek megfelelően.',
                    'icon' => 'warning',
                ]);
                return;
            }
        }

        // Több rendelés PDF generálása
        $template = $language === 'ro' ? 'pdf.order_ro' : 'pdf.order';

        // Mentjük az eredeti nyelvet
        $originalLocale = App::getLocale();
        
        // Beállítjuk a PDF nyelvét
        App::setLocale($language);

        $pdf = Pdf::loadView('pdf.orders_bulk', [
            'orders' => $orders,
            'language' => $language
        ]);
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');
        $filename = $language === 'ro' ? 'comenzi.pdf' : 'rendelesek.pdf';
        
        // Visszaállítjuk az eredeti nyelvet
        App::setLocale($originalLocale);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function generateProductsSummaryPDF($language = 'hu')
    {
        // Szűrt rendelések lekérése - minden rendelést figyelembe veszünk, nem csak a teljesítetteket
        $query = Order::with(['orderDetails.product', 'store', 'user'])
            ->search($this->search);

        if (!empty($this->statusFilter)) {
            $query->filterStatus($this->statusFilter);
        }
        if (!empty($this->storeFilter)) {
            $query->where('store_id', $this->storeFilter);
        }
        if (!empty($this->userFilter)) {
            $query->where('user_id', $this->userFilter);
        }
        if (!empty($this->dateStart)) {
            $query->whereDate('created_at', '>=', $this->dateStart);
        }
        if (!empty($this->dateEnd)) {
            $query->whereDate('created_at', '<=', $this->dateEnd);
        }
        
        $orders = $query->get();
        
        if ($orders->isEmpty()) {
            $this->notification()->send([
                'title' => 'Nincs találat',
                'description' => 'Nincs rendelés a szűrési feltételeknek megfelelően.',
                'icon' => 'warning',
            ]);
            return;
        }

        // Termékek összesítése
        $productsSummary = [];
        
        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                if ($detail->product) {
                    $productId = $detail->product->id;
                    $productName = $detail->product->name;
                    $accountingCode = $detail->product->accounting_code;
                    
                    // Mennyiség számítása - mindig a megfelelő mennyiséget használjuk
                    $quantity = 0;
                    
                    // Ha van dispatched_quantity és nagyobb mint 0, akkor azt használjuk
                    if ($detail->dispatched_quantity > 0) {
                        $quantity = $detail->dispatched_quantity;
                    } 
                    // Egyébként a quantity-t használjuk
                    else {
                        $quantity = $detail->quantity;
                    }
                    
                    // Csak akkor adjuk hozzá, ha a mennyiség nagyobb mint 0
                    if ($quantity > 0) {
                        if (!isset($productsSummary[$productId])) {
                            $productsSummary[$productId] = [
                                'name' => $productName,
                                'accounting_code' => $accountingCode,
                                'total_quantity' => 0
                            ];
                        }
                        
                        $productsSummary[$productId]['total_quantity'] += $quantity;
                    }
                }
            }
        }

        // Rendezés név szerint - átalakítjuk indexelt array-vé
        $productsSummary = array_values($productsSummary);
        usort($productsSummary, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Mentjük az eredeti nyelvet
        $originalLocale = App::getLocale();
        
        // Beállítjuk a PDF nyelvét
        App::setLocale($language);

        // Szűrők neveinek lekérése
        $statusName = '';
        if (!empty($this->statusFilter)) {
            $statusEnum = OrderStatuses::tryFrom($this->statusFilter);
            $statusName = $statusEnum ? $statusEnum->label() : $this->statusFilter;
        }
        
        $storeName = '';
        if (!empty($this->storeFilter)) {
            $store = Store::find($this->storeFilter);
            $storeName = $store ? $store->name : '';
        }
        
        $userName = '';
        if (!empty($this->userFilter)) {
            $user = \App\Models\User::find($this->userFilter);
            $userName = $user ? $user->name : '';
        }

        $pdf = Pdf::loadView('pdf.products_summary', [
            'products' => $productsSummary,
            'language' => $language,
            'filters' => [
                'search' => $this->search,
                'status' => $statusName,
                'store' => $storeName,
                'user' => $userName,
                'dateStart' => $this->dateStart,
                'dateEnd' => $this->dateEnd
            ]
        ]);
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');
        $filename = $language === 'ro' ? 'produse_sumar.pdf' : 'termekek_osszesites.pdf';
        
        // Visszaállítjuk az eredeti nyelvet
        App::setLocale($originalLocale);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function generateOrderProductsSummaryPDF($orderId, $language = 'hu')
    {
        $order = Order::with(['orderDetails.product', 'store', 'user'])->find($orderId);
        if (!$order) {
            $this->notification()->send([
                'title' => 'Hiba',
                'description' => 'A rendelés nem található.',
                'icon' => 'error',
            ]);
            return;
        }

        // Termékek összesítése az adott rendeléshez
        $productsSummary = [];
        
        foreach ($order->orderDetails as $detail) {
            if ($detail->product) {
                $productId = $detail->product->id;
                $productName = $detail->product->name;
                $accountingCode = $detail->product->accounting_code;
                
                // Mennyiség számítása - mindig a megfelelő mennyiséget használjuk
                $quantity = 0;
                
                // Ha van dispatched_quantity és nagyobb mint 0, akkor azt használjuk
                if ($detail->dispatched_quantity > 0) {
                    $quantity = $detail->dispatched_quantity;
                } 
                // Egyébként a quantity-t használjuk
                else {
                    $quantity = $detail->quantity;
                }
                
                // Csak akkor adjuk hozzá, ha a mennyiség nagyobb mint 0
                if ($quantity > 0) {
                    if (!isset($productsSummary[$productId])) {
                        $productsSummary[$productId] = [
                            'name' => $productName,
                            'accounting_code' => $accountingCode,
                            'total_quantity' => 0
                        ];
                    }
                    
                    $productsSummary[$productId]['total_quantity'] += $quantity;
                }
            }
        }

        // Rendezés név szerint - átalakítjuk indexelt array-vé
        $productsSummary = array_values($productsSummary);
        usort($productsSummary, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Mentjük az eredeti nyelvet
        $originalLocale = App::getLocale();
        
        // Beállítjuk a PDF nyelvét
        App::setLocale($language);

        $pdf = Pdf::loadView('pdf.order_products_summary', [
            'order' => $order,
            'language' => $language
        ]);
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');
        $filename = $language === 'ro' ? 'produse_sumar_comanda_' . $orderId . '.pdf' : 'termekek_osszesites_rendeles_' . $orderId . '.pdf';
        
        // Visszaállítjuk az eredeti nyelvet
        App::setLocale($originalLocale);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function getStores()
    {
        return Store::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin.order.order-list', [
            'orders'   => $this->getOrders(),
            'statuses' => OrderStatuses::toArray(),
            'stores'   => $this->getStores(),
        ]);
    }
}

