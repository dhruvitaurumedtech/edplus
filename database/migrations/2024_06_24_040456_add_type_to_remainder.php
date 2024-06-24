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
        Schema::table('remainder', function (Blueprint $table) {
            $table->unsignedTinyInteger('type_field')->comment('1: remainder, 2: greeting')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remainder', function (Blueprint $table) {
            //
        });
    }
};
