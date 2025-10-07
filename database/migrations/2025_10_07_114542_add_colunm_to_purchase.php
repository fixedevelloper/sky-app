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
        Schema::table('paiements', function (Blueprint $table) {
            $table->string('reference_id')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->enum('status', ['pending', 'waiting','confirmed', 'failed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase', function (Blueprint $table) {
            //
        });
    }
};
