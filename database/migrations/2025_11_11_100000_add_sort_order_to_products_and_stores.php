<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('price');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('contact_person');
        });

        $products = DB::table('products')
            ->select('id')
            ->orderBy('id')
            ->get();

        foreach ($products as $index => $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['sort_order' => $index + 1]);
        }

        $stores = DB::table('stores')
            ->select('id')
            ->orderBy('id')
            ->get();

        foreach ($stores as $index => $store) {
            DB::table('stores')
                ->where('id', $store->id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};


