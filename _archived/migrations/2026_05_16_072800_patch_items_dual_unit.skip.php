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
        // 1. Safe Column Addition
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'unit_kecil_id')) {
                $table->foreignId('unit_kecil_id')->after('category_id')->nullable()->constrained('units')->nullOnDelete();
            }
            if (!Schema::hasColumn('items', 'unit_besar_id')) {
                $table->foreignId('unit_besar_id')->after('unit_kecil_id')->nullable()->constrained('units')->nullOnDelete();
            }
            if (!Schema::hasColumn('items', 'harga_kecil')) {
                $table->bigInteger('harga_kecil')->default(0)->after('unit_besar_id');
            }
            if (!Schema::hasColumn('items', 'harga_besar')) {
                $table->bigInteger('harga_besar')->default(0)->after('harga_kecil');
            }
            if (!Schema::hasColumn('items', 'stok_kecil')) {
                $table->integer('stok_kecil')->default(0)->after('stok_minimal');
            }
            if (!Schema::hasColumn('items', 'stok_besar')) {
                $table->integer('stok_besar')->default(0)->after('stok_kecil');
            }
            if (!Schema::hasColumn('items', 'budget_source_id')) {
                $table->foreignId('budget_source_id')->nullable()->after('harga_besar')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('items', 'item_location_id')) {
                $table->foreignId('item_location_id')->nullable()->after('budget_source_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('items', 'tgl_diterima')) {
                $table->date('tgl_diterima')->nullable()->after('tgl_kadaluarsa');
            }
        });

        // 2. Safe Data Migration: Copy unit_id to unit_kecil_id
        if (Schema::hasColumn('items', 'unit_id') && Schema::hasColumn('items', 'unit_kecil_id')) {
            DB::statement('UPDATE items SET unit_kecil_id = unit_id WHERE unit_kecil_id IS NULL');
        }

        // 3. Safe Cleanup
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'unit_id')) {
                // Try-catch for foreign key drop to handle cases where constraint name might differ
                try {
                    $table->dropForeign(['unit_id']);
                } catch (\Exception $e) {}
                
                $table->dropColumn('unit_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('category_id')->constrained('units')->cascadeOnDelete();
            }
        });

        // Restore data
        if (Schema::hasColumn('items', 'unit_id') && Schema::hasColumn('items', 'unit_kecil_id')) {
            DB::statement('UPDATE items SET unit_id = unit_kecil_id WHERE unit_id IS NULL');
        }

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'unit_kecil_id')) {
                try { $table->dropForeign(['unit_kecil_id']); } catch (\Exception $e) {}
                $table->dropColumn('unit_kecil_id');
            }
            if (Schema::hasColumn('items', 'unit_besar_id')) {
                try { $table->dropForeign(['unit_besar_id']); } catch (\Exception $e) {}
                $table->dropColumn('unit_besar_id');
            }
            if (Schema::hasColumn('items', 'budget_source_id')) {
                try { $table->dropForeign(['budget_source_id']); } catch (\Exception $e) {}
                $table->dropColumn('budget_source_id');
            }
            if (Schema::hasColumn('items', 'item_location_id')) {
                try { $table->dropForeign(['item_location_id']); } catch (\Exception $e) {}
                $table->dropColumn('item_location_id');
            }
            
            $colsToDrop = ['harga_kecil', 'harga_besar', 'stok_kecil', 'stok_besar', 'tgl_diterima'];
            foreach ($colsToDrop as $col) {
                if (Schema::hasColumn('items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
