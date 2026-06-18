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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gudang');
            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan'); // e.g., PCS, Box, Kg
            $table->string('simbol')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique()->index();
            $table->string('nama_barang');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_kecil_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('unit_besar_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('harga_kecil', 15, 2)->nullable();
            $table->decimal('harga_besar', 15, 2)->nullable();
            $table->integer('stok_kecil')->default(0);
            $table->integer('stok_besar')->default(0);
            $table->integer('stok_minimal')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->string('qr_code')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->date('tgl_diterima')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi')->unique(); // e.g., BM-001, BK-001
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Operator
            $table->enum('jenis', ['masuk', 'keluar', 'penyesuaian']);
            $table->integer('jumlah_kecil');
            $table->integer('jumlah_besar')->default(0);
            $table->string('penerima_penyerah')->nullable();
            $table->string('keperluan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('tgl_transaksi');
            $table->timestamps();
        });

        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Auditor
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('items');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('warehouses');
    }
};
