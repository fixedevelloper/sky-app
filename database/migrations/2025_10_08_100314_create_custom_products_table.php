<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('exact_amount', 15, 2)->nullable();
            $table->foreignId('purchase_id')
                ->unique() // clé unique pour one-to-one
                ->nullable()
                ->constrained('purchases')
                ->nullOnDelete();
            $table->timestamps();
        });

        // Ajout d'un flag pour savoir si c'est un produit custom
        Schema::table('purchases', function (Blueprint $table) {
            $table->boolean('is_custom_product')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('is_custom_product');
        });

        Schema::dropIfExists('custom_products');
    }
};
