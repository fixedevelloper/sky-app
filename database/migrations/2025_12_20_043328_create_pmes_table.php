<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmes', function (Blueprint $table) {
            $table->id();

            // 🔑 Référence paiement
            $table->uuid('referenceId')->unique();

            // 💳 Opérateur de paiement
            $table->enum('operator', ['MTN', 'ORANGE'])->nullable();

            // 🏢 Informations entreprise
            $table->string('name_entreprise');
            $table->string('name_responsable');
            $table->string('poste_responsable');

            // 💰 Informations financières
            $table->decimal('amount_bc', 15, 2);
            $table->integer('number_souscripteur');
            $table->integer('number_echeance_paiement');

            // 💵 Montant total à payer (calculé backend)
            $table->decimal('montant_total', 15, 2);

            // 👥 Gestion
            $table->string('name_gestionnaire');
            $table->string('name_manager');

            // 📂 Pièces jointes
            $table->string('image_bc');
            $table->string('image_bl');
            $table->string('image_facture');

            // 🔄 Statut du paiement
            $table->enum('status', ['pending', 'confirmed', 'failed'])
                ->default('pending');

            // 👤 Vendeur
            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ✅ Date de confirmation paiement
            $table->timestamp('confirmed_at')->nullable();

            // 🕒 Dates
            $table->timestamps();
        });
        Schema::table('point_sales', function (Blueprint $table) {
            // ✅ Date de confirmation paiement
            $table->timestamp('confirmed_at')->nullable()->after('vendor_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('pmes');
    }
};
