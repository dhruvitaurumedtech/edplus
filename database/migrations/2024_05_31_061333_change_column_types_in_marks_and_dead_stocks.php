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
        Schema::table('marks', function (Blueprint $table) {
            $table->float('mark')->change();
        });

        Schema::table('dead_stocks', function (Blueprint $table) {
            $table->integer('no_of_item')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->previous_data_type('mark')->change(); 
        });

        Schema::table('dead_stocks', function (Blueprint $table) {
            $table->previous_data_type('no_of_item')->change(); 
        });
    }
};
