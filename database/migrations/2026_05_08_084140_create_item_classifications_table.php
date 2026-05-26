<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_type_id')->constrained()->cascadeOnDelete();
            $table->string('nama_klasifikasi');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_classifications');
    }
};
