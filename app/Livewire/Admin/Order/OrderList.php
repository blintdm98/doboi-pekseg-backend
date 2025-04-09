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

    public function getOrders()
    {
        return Order::with('store')->latest()->paginate(20);
    }

    public function showOrder(Order $order)
    {
        $this->selectedOrder = $order;
        $this->orderDetails = OrderDetail::with('product')
            ->where('order_id', $order->id)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'dispatched_quantity' => $item->dispatched_quantity,
            ])
            ->toArray();

        $this->orderModal = true;
    }

    public function save()
    {
        foreach ($this->orderDetails as $detail) {
            OrderDetail::where('id', $detail['id'])->update([
                'dispatched_quantity' => $detail['dispatched_quantity'] ?? 0,
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
}

