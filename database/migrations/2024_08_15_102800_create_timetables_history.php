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
        Schema::create('timetables_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->foreign('batch_id')->references('id')->on('batches');
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subject');
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->unsignedBigInteger('lecture_type');
            $table->foreign('lecture_type')->references('id')->on('lecture_type');
            $table->unsignedBigInteger('class_room_id');
            $table->foreign('class_room_id')->references('id')->on('class_room');
            $table->date('academic_end_date');
            $table->time('start_date')->nullable();
            $table->time('end_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('day');
            $table->foreign('day')->references('id')->on('days');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables_history');
    }
};
