<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('momo_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->nullable()->index();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->json('payload')->nullable(); // le contenu complet du callback
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('momo_callbacks');
    }
};

