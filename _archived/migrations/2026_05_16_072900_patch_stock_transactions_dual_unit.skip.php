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
        Schema::table('stock_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transactions', 'jumlah_kecil')) {
                $table->integer('jumlah_kecil')->default(0)->after('jenis');
            }
            if (!Schema::hasColumn('stock_transactions', 'jumlah_besar')) {
                $table->integer('jumlah_besar')->default(0)->after('jumlah_kecil');
            }
            if (!Schema::hasColumn('stock_transactions', 'first_party_id')) {
                $table->foreignId('first_party_id')->nullable()->after('user_id')->constrained('first_parties')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transactions', 'second_party_id')) {
                $table->foreignId('second_party_id')->nullable()->after('first_party_id')->constrained('second_parties')->nullOnDelete();
            }
        });

        // 2. Safe Data Migration: Copy jumlah to jumlah_kecil
        if (Schema::hasColumn('stock_transactions', 'jumlah') && Schema::hasColumn('stock_transactions', 'jumlah_kecil')) {
            DB::statement('UPDATE stock_transactions SET jumlah_kecil = jumlah WHERE jumlah_kecil = 0');
        }

        // 3. Safe Cleanup
        Schema::table('stock_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transactions', 'jumlah')) {
                $table->dropColumn('jumlah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transactions', 'jumlah')) {
                $table->integer('jumlah')->default(0)->after('jenis');
            }
        });

        // Restore data
        if (Schema::hasColumn('stock_transactions', 'jumlah') && Schema::hasColumn('stock_transactions', 'jumlah_kecil')) {
            DB::statement('UPDATE stock_transactions SET jumlah = jumlah_kecil WHERE jumlah = 0');
        }

        Schema::table('stock_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transactions', 'jumlah_kecil')) {
                $table->dropColumn('jumlah_kecil');
            }
            if (Schema::hasColumn('stock_transactions', 'jumlah_besar')) {
                $table->dropColumn('jumlah_besar');
            }
            if (Schema::hasColumn('stock_transactions', 'first_party_id')) {
                try { $table->dropForeign(['first_party_id']); } catch (\Exception $e) {}
                $table->dropColumn('first_party_id');
            }
            if (Schema::hasColumn('stock_transactions', 'second_party_id')) {
                try { $table->dropForeign(['second_party_id']); } catch (\Exception $e) {}
                $table->dropColumn('second_party_id');
            }
        });
    }
};
