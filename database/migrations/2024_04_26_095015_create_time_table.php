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
        Schema::create('time_table', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('medium_id');
            $table->unsignedBigInteger('institute_for');
            $table->unsignedBigInteger('standard_id');
            $table->unsignedBigInteger('stream_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('lecture_type');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_table');
    }
};
