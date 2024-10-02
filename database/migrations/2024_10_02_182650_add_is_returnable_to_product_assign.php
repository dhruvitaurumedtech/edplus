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
        Schema::table('products_assign', function (Blueprint $table) {
            $table->enum('is_returnable', ['0', '1'])->default('0')->after('return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_assign', function (Blueprint $table) {
            $table->dropColumn('is_returnable');
        });
    }
};
