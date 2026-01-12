<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cca_enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->integer('accounts');
            $table->string('niu')->nullable();
            $table->string('position');
            $table->json('documents')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {

    }

};
