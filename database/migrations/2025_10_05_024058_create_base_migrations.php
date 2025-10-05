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
        Schema::create('point_sales', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('activity')->nullable();
            $table->string('localisation')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image_doc_fiscal')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained("users", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique(); // 📌 éviter doublons
            $table->string('activity')->nullable();
            $table->string('localisation')->nullable();
            $table->string('commercial_code')->nullable()->index();
            $table->string('code_key_account')->nullable()->index();
            $table->string('image_cni_recto')->nullable();
            $table->string('image_cni_verso')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('point_sale_id')->nullable()->constrained("point_sales", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->decimal('price', 10, 2)->default(0); // 💰 prix en décimal
            $table->decimal('amount_by_day',10, 2)->default(0); // 🔄 remplacé amount_by_day par quantity
            $table->string('payment_mode')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained("customers", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('amount_rest', 10, 2)->default(0);
            $table->string('operator')->nullable()->index();
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending'); // ✅ statut du paiement
            $table->foreignId('purchase_id')->nullable()->constrained("purchases", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🔄 ordre inverse de création pour éviter les erreurs de contrainte
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('point_sales');
    }
};
