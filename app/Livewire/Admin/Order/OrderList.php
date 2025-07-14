<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use App\Models\OrderDetail;
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

    public function getOrders()
    {
        $search = trim($this->search);

        return Order::query()
            ->with(['store', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                        ->orWhereHas('store', fn($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(20);
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
                'dispatched_quantity' => ($order->status === 'pending' && $item->dispatched_quantity === 0)
                    ? $item->quantity
                    : $item->dispatched_quantity,
            ])
            ->toArray();

        // Csak akkor töltjük be a rendelkezésre álló termékeket, ha a rendelés nincs teljesítve
        if ($order->status !== 'completed') {
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
                'status' => $isComplete ? 'completed' : 'partial',
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

    public function render()
    {
        $query = Order::with(['user', 'store', 'orderDetails', 'orderDetails.product']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('status', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->orWhereHas('store', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->orWhereHas('orderDetails.product', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));;
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
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

        return view('livewire.admin.order.order-list', [
            'orders' => $query->latest()->paginate(50),
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

        OrderDetail::where('order_id', $orderId)->delete();

        $order->delete();

        $this->orderModal = false;
        $this->selectedOrder = null;
        $this->orderDetails = [];

        $this->notification()->send([
            'title' => __('common.deleted_successfully'),
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
    }

    public function generatePDF($orderId, $language = 'hu')
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

        // Számítsuk ki a teljes összeget (csak a létező termékeket számolva, kiküldött mennyiségekkel)
        $total = $order->orderDetails->sum(function($detail) {
            if ($detail->product) {
                $quantity = $detail->dispatched_quantity > 0 ? $detail->dispatched_quantity : $detail->quantity;
                return $quantity * $detail->product->price;
            }
            return 0;
        });

        // Válasszuk ki a megfelelő template-et a nyelv alapján
        $template = $language === 'ro' ? 'pdf.order_ro' : 'pdf.order';

        // Generáljuk a PDF-t
        $pdf = Pdf::loadView($template, [
            'order' => $order,
            'total' => $total
        ]);

        // Beállítjuk a karakterkódolást
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Fájlnév a nyelv alapján
        $filename = $language === 'ro' ? 'comanda_' . $order->id . '.pdf' : 'rendeles_' . $order->id . '.pdf';

        // Töltsük le a PDF-t
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}

