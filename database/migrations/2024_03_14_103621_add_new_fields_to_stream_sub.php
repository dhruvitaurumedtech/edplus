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
        Schema::table('stream_sub', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_for_id')->nullable()->after('institute_id');
            $table->foreign('institute_for_id')->references('id')->on('institute_for');
            $table->unsignedBigInteger('board_id')->nullable()->after('institute_for_id');
            $table->foreign('board_id')->references('id')->on('board');
            $table->unsignedBigInteger('medium_id')->nullable()->after('board_id');
            $table->foreign('medium_id')->references('id')->on('medium');
            $table->unsignedBigInteger('class_id')->nullable()->after('medium_id');
            $table->foreign('class_id')->references('id')->on('class');
            $table->unsignedBigInteger('standard_id')->nullable()->after('medium_id');
            $table->foreign('standard_id')->references('id')->on('standard');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stream_sub', function (Blueprint $table) {
            $table->dropColumn('institute_for_id');
            $table->dropColumn('board_id');
            $table->dropColumn('medium_id');
            $table->dropColumn('class_id');
            $table->dropColumn('standard_id');
        });
    }
};
