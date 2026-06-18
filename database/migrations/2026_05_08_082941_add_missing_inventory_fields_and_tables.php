<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add kode_gudang to warehouses
        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'kode_gudang')) {
                $table->string('kode_gudang')->unique()->after('id');
            }
        });

        // Add deskripsi to categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('nama_kategori');
            }
        });

        // Create Item Locations (Rak/Posisi)
        Schema::create('item_locations', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Create Budget Sources (Sumber Anggaran)
        Schema::create('budget_sources', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Create Pihak Pertama
        Schema::create('first_parties', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pihak');
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('instansi')->nullable();
            $table->timestamps();
        });

        // Create Pihak Kedua
        Schema::create('second_parties', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pihak');
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('instansi')->nullable();
            $table->timestamps();
        });

        // Add location & budget source foreign keys to items table
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('item_location_id')->nullable()->after('qr_code')->constrained('item_locations')->nullOnDelete();
            $table->foreignId('budget_source_id')->nullable()->after('qr_code')->constrained('budget_sources')->nullOnDelete();
        });

        // Add first & second party foreign keys to stock_transactions table
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreignId('first_party_id')->nullable()->after('user_id')->constrained('first_parties')->nullOnDelete();
            $table->foreignId('second_party_id')->nullable()->after('user_id')->constrained('second_parties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['item_location_id']);
            $table->dropColumn('item_location_id');
            $table->dropForeign(['budget_source_id']);
            $table->dropColumn('budget_source_id');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['first_party_id']);
            $table->dropColumn('first_party_id');
            $table->dropForeign(['second_party_id']);
            $table->dropColumn('second_party_id');
        });

        Schema::dropIfExists('second_parties');
        Schema::dropIfExists('first_parties');
        Schema::dropIfExists('budget_sources');
        Schema::dropIfExists('item_locations');
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('kode_gudang');
        });
    }
};
