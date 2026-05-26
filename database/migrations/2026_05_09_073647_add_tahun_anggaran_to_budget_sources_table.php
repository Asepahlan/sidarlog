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
        Schema::table('budget_sources', function (Blueprint $table) {
            $table->string('tahun_anggaran', 4)->nullable()->after('nama_sumber');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_sources', function (Blueprint $table) {
            $table->dropColumn('tahun_anggaran');
        });
    }
};
