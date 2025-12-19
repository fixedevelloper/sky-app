<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */


    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('memory')->nullable();
            $table->integer('price')->default(0);
            $table->integer('price_leasing')->default(0);
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->nullable()->constrained("categories", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('point_sales', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('activity')->nullable();
            $table->string('localisation')->nullable();
            $table->string('phone')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image_doc_fiscal')->nullable();
            $table->string('operator')->nullable()->index();
            $table->string('referenceId')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->foreignId('vendor_id')->nullable()->constrained("users", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->json('categories')->nullable();
            $table->foreignId('user_id')->nullable()->constrained("users", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('image_cni_recto')->nullable();
            $table->string('image_cni_verso')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('user_id')->nullable()->constrained("users", 'id')->nullOnDelete();
            $table->foreignId('point_sale_id')->nullable()->constrained("point_sales", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->enum('pay_type', ['cash', 'leasing']);
            $table->string('payment_mode')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_custom_product')->default(false);
            $table->foreignId('product_id')->nullable()->constrained("products", 'id')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained("customers", 'id')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained("users", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('amount_rest', 10, 2)->default(0);
            $table->string('operator')->nullable()->index();
            $table->string('reference_id')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->enum('status', ['pending', 'waiting', 'confirmed', 'failed'])->default('pending');
            $table->foreignId('purchase_id')->nullable()->constrained("purchases", 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('momo_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->nullable()->index();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->json('payload')->nullable(); // le contenu complet du callback
            $table->timestamps();
        });
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
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🔄 ordre inverse de création pour éviter les erreurs de contrainte
        Schema::dropIfExists('products');
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('point_sales');
    }
};
