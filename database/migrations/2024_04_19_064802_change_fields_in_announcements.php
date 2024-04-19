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
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['batch_id']);

            //$table->string('batch_id')->after('user_id');
        });

        Schema::table('announcements', function (Blueprint $table) {
            
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['subject_id']);

            //$table->string('subject_id')->after('stream_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreign('batch_id')->references('id')->on('batches');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->foreign('subject_id')->references('id')->on('subject');
        });
    }
};
