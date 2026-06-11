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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['lost', 'found']); // 'lost' = barang hilang, 'found' = barang ditemukan
            $table->string('name');                  // nama barang
            $table->text('description');             // deskripsi barang
            $table->string('location');              // lokasi hilang/ditemukan
            $table->date('date_occurred');           // tanggal kejadian
            $table->string('photo')->nullable();     // path foto barang
            $table->enum('status', ['open', 'resolved'])->default('open'); // status postingan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
