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
        $this->safeRename('items', [
            'category_id' => 'kategori_id',
            'unit_kecil_id' => 'satuan_kecil_id',
            'item_location_id' => 'lokasi_barang_id',
            'budget_source_id' => 'sumber_anggaran_id',
            'harga_kecil' => 'harga_satuan_kecil',
            'stok_kecil' => 'stok_saat_ini_kecil',
        ]);

        $this->safeRename('stock_transactions', [
            'item_id' => 'barang_id',
            'warehouse_id' => 'gudang_id',
            'user_id' => 'pengguna_id',
            'first_party_id' => 'pihak_kesatu_id',
            'second_party_id' => 'pihak_kedua_id',
            'jumlah_kecil' => 'jumlah_barang_kecil',
        ]);

        Schema::table('stock_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transactions', 'jumlah')) {
                $table->dropColumn('jumlah');
            }
        });

        $this->safeRename('stock_opnames', [
            'item_id' => 'barang_id',
            'warehouse_id' => 'gudang_id',
            'user_id' => 'pengguna_id',
        ]);



        $this->safeRename('item_types', [
            'category_id' => 'kategori_id',
        ]);

        $this->safeRename('item_classifications', [
            'item_type_id' => 'jenis_barang_id',
        ]);

        $this->safeRename('activity_logs', [
            'user_id' => 'pengguna_id',
        ]);
    }

    public function down(): void
    {
        $this->safeRename('items', [
            'kategori_id' => 'category_id',
            'satuan_kecil_id' => 'unit_kecil_id',
            'lokasi_barang_id' => 'item_location_id',
            'sumber_anggaran_id' => 'budget_source_id',
            'harga_satuan_kecil' => 'harga_kecil',
            'stok_saat_ini_kecil' => 'stok_kecil',
        ]);

        $this->safeRename('stock_transactions', [
            'barang_id' => 'item_id',
            'gudang_id' => 'warehouse_id',
            'pengguna_id' => 'user_id',
            'pihak_kesatu_id' => 'first_party_id',
            'pihak_kedua_id' => 'second_party_id',
            'jumlah_barang_kecil' => 'jumlah_kecil',
        ]);

        Schema::table('stock_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transactions', 'jumlah')) {
                $table->integer('jumlah')->default(0)->after('jenis');
            }
        });

        $this->safeRename('stock_opnames', [
            'barang_id' => 'item_id',
            'gudang_id' => 'warehouse_id',
            'pengguna_id' => 'user_id',
        ]);



        $this->safeRename('item_types', [
            'kategori_id' => 'category_id',
        ]);

        $this->safeRename('item_classifications', [
            'jenis_barang_id' => 'item_type_id',
        ]);

        $this->safeRename('activity_logs', [
            'pengguna_id' => 'user_id',
        ]);
    }

    private function safeRename($tableName, $columns)
    {
        if (!Schema::hasTable($tableName)) return;

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
            foreach ($columns as $old => $new) {
                if (Schema::hasColumn($tableName, $old) && !Schema::hasColumn($tableName, $new)) {
                    $table->renameColumn($old, $new);
                }
            }
        });
    }
};
