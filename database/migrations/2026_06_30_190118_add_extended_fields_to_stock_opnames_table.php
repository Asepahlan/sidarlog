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
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->string('nomor_stock_opname')->nullable()->after('id');
            $table->string('periode')->nullable()->after('nomor_stock_opname');
            $table->date('tgl_opname')->nullable()->after('periode');
            $table->string('kondisi_barang')->nullable()->after('selisih');
            
            // Kolom tanda tangan
            $table->string('petugas_nama')->nullable()->after('keterangan');
            $table->string('petugas_nip')->nullable()->after('petugas_nama');
            $table->string('kepala_gudang_nama')->nullable()->after('petugas_nip');
            $table->string('kepala_gudang_nip')->nullable()->after('kepala_gudang_nama');
            $table->string('mengetahui_nama')->nullable()->after('kepala_gudang_nip');
            $table->string('mengetahui_nip')->nullable()->after('mengetahui_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_stock_opname',
                'periode',
                'tgl_opname',
                'kondisi_barang',
                'petugas_nama',
                'petugas_nip',
                'kepala_gudang_nama',
                'kepala_gudang_nip',
                'mengetahui_nama',
                'mengetahui_nip'
            ]);
        });
    }
};
