<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Kolom reference_bap_id — nullable agar transaksi masuk (tanpa BAP) tetap valid
            $table->foreignId('reference_bap_id')
                  ->nullable()
                  ->after('pihak_kedua_id')
                  ->constrained('reference_baps')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['reference_bap_id']);
            $table->dropColumn('reference_bap_id');
        });
    }
};
