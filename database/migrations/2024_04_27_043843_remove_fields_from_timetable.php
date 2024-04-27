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
        Schema::table('time_table', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('institute_id');
            $table->dropColumn('board_id');
            $table->dropColumn('medium_id');
            $table->dropColumn('institute_for');
            $table->dropColumn('standard_id');
            $table->dropColumn('stream_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetable', function (Blueprint $table) {
            //
        });
    }
};
