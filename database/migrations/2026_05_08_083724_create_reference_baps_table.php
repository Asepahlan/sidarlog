<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_baps', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ba')->unique();
            $table->string('judul_ba');
            $table->date('tgl_ba');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_baps');
    }
};
