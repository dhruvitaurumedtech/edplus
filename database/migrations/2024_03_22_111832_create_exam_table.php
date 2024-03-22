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
        Schema::create('exam', function (Blueprint $table) {
            $table->id();
            $table->string('exam_title');
            $table->string('total_mark');
            $table->string('exam_type');
            $table->date('exam_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('institute_for_id');
            $table->string('board_id');
            $table->string('medium_id');
            $table->string('class_id');
            $table->string('standard_id');
            $table->string('stream_id')->nullable();
            $table->string('subject_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam');
    }
};
