<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('point_sales', function (Blueprint $table) {
            $table->dropUnique('point_sales_name_unique');
            $table->unique(['vendor_id', 'name']);
        });
    }

    public function down()
    {
        Schema::table('point_sales', function (Blueprint $table) {
            $table->dropUnique(['vendor_id', 'name']);
            $table->unique('name');
        });
    }

};
