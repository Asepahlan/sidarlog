<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop item_classifications first because it references item_types
        Schema::dropIfExists('item_classifications');
        Schema::dropIfExists('item_types');
    }

    public function down(): void
    {
        Schema::create('item_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('nama_jenis');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('item_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_type_id')->constrained()->cascadeOnDelete();
            $table->string('nama_klasifikasi');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }
};
