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
        Schema::table('time_table_base', function (Blueprint $table) {
            $table->unsignedBigInteger('class_room_id')->nullable()->after('batch_id');
            $table->foreign('class_room_id')->references('id')->on('class_room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_table_base', function (Blueprint $table) {
            $table->dropForeign(['class_room_id']);
            $table->dropColumn('class_room_id');
        });
    }
};
