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
        Schema::table('medium_sub', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_for_id')->nullable()->after('institute_id');
            $table->foreign('institute_for_id')->references('id')->on('institute_for');
            $table->unsignedBigInteger('board_id')->nullable()->after('institute_for_id');
            $table->foreign('board_id')->references('id')->on('board');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medium_sub', function (Blueprint $table) {
            $table->dropColumn('institute_for_id');
            $table->dropColumn('board_id');
        });
    }
};
