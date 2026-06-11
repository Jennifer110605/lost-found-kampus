<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pelapor klaim
            $table->text('description');                   // bukti kepemilikan
            $table->string('proof_photo')->nullable();     // foto bukti opsional
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();        // catatan admin saat approve/reject
            $table->string('handover_photo')->nullable();  // foto serah terima (ber-watermark)
            $table->timestamp('handover_at')->nullable();  // waktu serah terima
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_requests');
    }
};
