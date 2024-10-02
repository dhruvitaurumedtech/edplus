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
            $table->date('return_date')->nullable()->after('quantity');
            $table->enum('is_returnable', ['0', '1'])->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_assign', function (Blueprint $table) {
            $table->dropColumn('return_date');
            $table->dropColumn('is_returnable');
        });
    }
};
