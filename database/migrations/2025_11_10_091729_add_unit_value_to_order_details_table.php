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
            $table->decimal('unit_value', 10, 2)->default(1)->after('tva');
        });

        OrderDetail::with('product')->chunkById(500, function ($details) {
            foreach ($details as $detail) {
                $unitValue = 1;

                if ($detail->product) {
                    if ($detail->product->unit === 'kg') {
                        $unitValue = $detail->product->unit_value ?: 1;
                    }
                }

                $detail->unit_value = $unitValue;
                $detail->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('unit_value');
        });
    }
};
