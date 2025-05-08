<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use App\Models\OrderDetail;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class OrderList extends Component
{
    use WithPagination, WireUiActions;

    public $orderModal = false;

    public $selectedOrder;
    public $orderDetails = [];
    public $search = '';
    public $statusFilter = '';


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
                'dispatched_quantity' => $item->dispatched_quantity,
            ])
            ->toArray();

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
        }

        $this->orderModal = false;

        $this->notification()->send([
            'title' => __('common.saved_successfully'),
            'icon' => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.order.order-list', [
            'orders' => $this->getOrders(),
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
}

