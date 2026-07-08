<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->string('penyerah_nama')->nullable()->after('catatan');
            $table->string('penyerah_nip')->nullable()->after('penyerah_nama');
            $table->string('penyerah_jabatan')->nullable()->after('penyerah_nip');
            $table->string('penyerah_alamat')->nullable()->after('penyerah_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn(['penyerah_nama', 'penyerah_nip', 'penyerah_jabatan', 'penyerah_alamat']);
        });
    }
};
