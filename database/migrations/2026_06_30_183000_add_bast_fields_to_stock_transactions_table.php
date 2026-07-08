<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->string('nomor_berita_acara')->nullable()->after('penerima_id');
            $table->string('mode_tanggal')->default('otomatis')->after('nomor_berita_acara');
            $table->string('hari')->nullable()->after('mode_tanggal');
            $table->string('tanggal')->nullable()->after('hari');
            $table->string('bulan')->nullable()->after('tanggal');
            $table->string('tahun')->nullable()->after('bulan');
            $table->string('kecamatan')->nullable()->after('tahun');
            $table->string('desa')->nullable()->after('kecamatan');
            $table->text('catatan')->nullable()->after('desa');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_berita_acara',
                'mode_tanggal',
                'hari',
                'tanggal',
                'bulan',
                'tahun',
                'kecamatan',
                'desa',
                'catatan',
            ]);
        });
    }
};
