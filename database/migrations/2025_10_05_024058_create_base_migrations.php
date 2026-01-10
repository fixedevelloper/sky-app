<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        // ---------------------------
        // 2️⃣ CATEGORIES
        // ---------------------------
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // ---------------------------
        // 3️⃣ POINT SALES
        // ---------------------------
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
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ---------------------------
        // 4️⃣ PRODUCTS
        // ---------------------------
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('memory')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('price_commercial', 10, 2)->default(0);
            $table->decimal('price_pme', 10, 2)->default(0);
            $table->decimal('price_distribute', 10, 2)->default(0);
            $table->decimal('price_leasing', 10, 2)->default(0);
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ---------------------------
        // 5️⃣ PARTNERS
        // ---------------------------
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot partners / categories
        Schema::create('partner_category', function (Blueprint $table) {
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['partner_id','category_id']);
        });

        // ---------------------------
        // 6️⃣ CUSTOMERS
        // ---------------------------
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('image_cni_recto')->nullable();
            $table->string('image_cni_verso')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('point_sale_id')->nullable()->constrained('point_sales')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ---------------------------
        // 7️⃣ ORDERS
        // ---------------------------
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('amount_rest', 10, 2)->default(0);
            $table->string('operator')->nullable()->index();
            $table->string('reference_id')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->enum('status', ['pending', 'waiting', 'confirmed', 'failed'])->default('pending');
            $table->json('meta')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ---------------------------
        // 8️⃣ ORDER ITEMS
        // ---------------------------
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ---------------------------
        // 9️⃣ PAIEMENTS
        // ---------------------------
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('amount_rest', 10, 2)->default(0);
            $table->string('operator')->nullable()->index();
            $table->string('reference_id')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->enum('status', ['pending', 'waiting', 'confirmed', 'failed'])->default('pending');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ---------------------------
        // 🔟 MOMO CALLBACKS
        // ---------------------------
        Schema::create('momo_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->nullable()->index();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        // ---------------------------
        // 1️⃣1️⃣ PMES
        // ---------------------------
        Schema::create('pmes', function (Blueprint $table) {
            $table->id();
            $table->uuid('referenceId')->unique();
            $table->enum('operator', ['MTN', 'ORANGE'])->nullable();
            $table->string('name_entreprise');
            $table->string('name_responsable');
            $table->string('poste_responsable');
            $table->decimal('amount_bc', 15, 2);
            $table->integer('number_souscripteur');
            $table->integer('number_echeance_paiement');
            $table->decimal('montant_total', 15, 2);
            $table->string('name_gestionnaire');
            $table->string('name_manager');
            $table->string('image_bc');
            $table->string('image_bl');
            $table->string('image_facture');
            $table->string('image_avi');
            $table->string('image_pl');
            $table->string('image_contract1');
            $table->string('image_contract2');
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_category');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('products');
        Schema::dropIfExists('point_sales');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('pmes');
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('momo_callbacks');
        Schema::dropIfExists('users');
    }
};
