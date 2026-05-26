<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Rename 'instansi' -> 'instansi_opd' if old column exists
            if (Schema::hasColumn('users', 'instansi') && !Schema::hasColumn('users', 'instansi_opd')) {
                $table->renameColumn('instansi', 'instansi_opd');
            }

            // 2. Rename 'jenis_asn' -> 'formasi_asn' if old column exists
            if (Schema::hasColumn('users', 'jenis_asn') && !Schema::hasColumn('users', 'formasi_asn')) {
                $table->renameColumn('jenis_asn', 'formasi_asn');
            }
        });

        // 3. Change column types (must be separate Schema::table call after rename)
        Schema::table('users', function (Blueprint $table) {
            // instansi_opd -> enum
            if (Schema::hasColumn('users', 'instansi_opd')) {
                $table->string('instansi_opd')->nullable()->default('bpbd')->change();
            }

            // formasi_asn -> string nullable
            if (Schema::hasColumn('users', 'formasi_asn')) {
                $table->string('formasi_asn')->nullable()->change();
            }

            // Ensure email is nullable
            if (Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->change();
            }

            // Add FK constraints if not already present
            if (Schema::hasColumn('users', 'jabatan_id') && Schema::hasTable('jabatans')) {
                // Make sure jabatan_id is nullable
                $table->unsignedBigInteger('jabatan_id')->nullable()->change();
            }

            if (Schema::hasColumn('users', 'bidang_id') && Schema::hasTable('bidangs')) {
                $table->unsignedBigInteger('bidang_id')->nullable()->change();
            }
        });

        // 4. Set default instansi_opd for existing users that have null
        DB::table('users')->whereNull('instansi_opd')->update(['instansi_opd' => 'bpbd']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'instansi_opd') && !Schema::hasColumn('users', 'instansi')) {
                $table->renameColumn('instansi_opd', 'instansi');
            }
            if (Schema::hasColumn('users', 'formasi_asn') && !Schema::hasColumn('users', 'jenis_asn')) {
                $table->renameColumn('formasi_asn', 'jenis_asn');
            }
        });
    }
};
