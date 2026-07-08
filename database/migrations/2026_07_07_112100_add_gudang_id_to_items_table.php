<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('items', 'gudang_id')) {
            Schema::table('items', function (Blueprint $table) {
                $table->foreignId('gudang_id')->nullable()->after('kategori_id')->constrained('warehouses')->nullOnDelete();
            });
        }
        
        // Drop old foreign key safely with DB statement
        try {
            DB::statement('ALTER TABLE items DROP FOREIGN KEY items_lokasi_barang_id_foreign');
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE items DROP FOREIGN KEY items_item_location_id_foreign');
            } catch (\Exception $ex) {
                // ignore
            }
        }
        
        if (Schema::hasColumn('items', 'lokasi_barang_id')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('lokasi_barang_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'gudang_id')) {
                $table->dropForeign(['gudang_id']);
                $table->dropColumn('gudang_id');
            }
            
            if (!Schema::hasColumn('items', 'lokasi_barang_id')) {
                $table->foreignId('lokasi_barang_id')->nullable()->after('kategori_id')->constrained('item_locations')->nullOnDelete();
            }
        });
    }
};
