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
    }

    public function down(): void
    {
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
