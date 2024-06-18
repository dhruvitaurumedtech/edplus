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
        Schema::create('general_timetable', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_room_id')->nullable();
            $table->foreign('class_room_id')->references('id')->on('class_room');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->foreign('batch_id')->references('id')->on('batches');
            $table->unsignedBigInteger('standard_id')->nullable();
            $table->foreign('standard_id')->references('id')->on('standard');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subject');
            $table->unsignedBigInteger('lecture_type');
            $table->foreign('lecture_type')->references('id')->on('lecture_type');
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
        Schema::dropIfExists('general_timetable');
    }
};
