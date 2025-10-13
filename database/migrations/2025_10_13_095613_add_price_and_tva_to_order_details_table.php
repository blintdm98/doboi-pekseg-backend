<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\OrderDetail;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('product_id');
            $table->integer('tva')->nullable()->after('price');
        });

        $orderDetails = OrderDetail::with('product')->get();
        foreach ($orderDetails as $detail) {
            if ($detail->product) {
                $detail->price = $detail->product->price;
                $detail->tva = $detail->product->tva ?? 11;
                $detail->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['price', 'tva']);
        });
    }
};
