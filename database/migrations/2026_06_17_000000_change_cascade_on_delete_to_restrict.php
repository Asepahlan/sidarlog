<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. stock_transactions
        $this->safelyDropForeign('stock_transactions', [
            'stock_transactions_barang_id_foreign',
            'stock_transactions_item_id_foreign',
            'stock_transactions_gudang_id_foreign',
            'stock_transactions_warehouse_id_foreign'
        ]);

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('items')->restrictOnDelete();
            $table->foreign('gudang_id')->references('id')->on('warehouses')->restrictOnDelete();
        });

        // 2. stock_opnames
        $this->safelyDropForeign('stock_opnames', [
            'stock_opnames_barang_id_foreign',
            'stock_opnames_item_id_foreign',
            'stock_opnames_gudang_id_foreign',
            'stock_opnames_warehouse_id_foreign'
        ]);

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('items')->restrictOnDelete();
            $table->foreign('gudang_id')->references('id')->on('warehouses')->restrictOnDelete();
        });

        // 3. stock_mutations
        $this->safelyDropForeign('stock_mutations', [
            'stock_mutations_barang_id_foreign',
            'stock_mutations_item_id_foreign',
            'stock_mutations_gudang_asal_id_foreign',
            'stock_mutations_from_warehouse_id_foreign',
            'stock_mutations_gudang_tujuan_id_foreign',
            'stock_mutations_to_warehouse_id_foreign'
        ]);

        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('items')->restrictOnDelete();
            $table->foreign('gudang_asal_id')->references('id')->on('warehouses')->restrictOnDelete();
            $table->foreign('gudang_tujuan_id')->references('id')->on('warehouses')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback to cascadeOnDelete
        $this->safelyDropForeign('stock_transactions', [
            'stock_transactions_barang_id_foreign',
            'stock_transactions_gudang_id_foreign'
        ]);

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('gudang_id')->references('id')->on('warehouses')->cascadeOnDelete();
        });

        $this->safelyDropForeign('stock_opnames', [
            'stock_opnames_barang_id_foreign',
            'stock_opnames_gudang_id_foreign'
        ]);

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('gudang_id')->references('id')->on('warehouses')->cascadeOnDelete();
        });

        $this->safelyDropForeign('stock_mutations', [
            'stock_mutations_barang_id_foreign',
            'stock_mutations_gudang_asal_id_foreign',
            'stock_mutations_gudang_tujuan_id_foreign'
        ]);

        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('gudang_asal_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->foreign('gudang_tujuan_id')->references('id')->on('warehouses')->cascadeOnDelete();
        });
    }

    /**
     * Drop list of foreign keys safely.
     */
    private function safelyDropForeign(string $tableName, array $foreignKeys): void
    {
        foreach ($foreignKeys as $fk) {
            try {
                Schema::table($tableName, function (Blueprint $table) use ($fk) {
                    $table->dropForeign($fk);
                });
            } catch (\Exception $e) {
                // Fail silently if constraint doesn't exist
            }
        }
    }
};
