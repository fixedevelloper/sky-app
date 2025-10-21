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
        Schema::table('point_sales', function (Blueprint $table) {
            $table->string('operator')->nullable()->index();
            $table->string('referenceId')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
