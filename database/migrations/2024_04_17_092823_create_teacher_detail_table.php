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
        Schema::create('teacher_detail', function (Blueprint $table) {
            $table->id();
            $table->string('institute_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->unsignedBigInteger('institute_for_id')->nullable();
            $table->foreign('institute_for_id')->references('id')->on('institute_for');
            $table->unsignedBigInteger('board_id')->nullable();
            $table->foreign('board_id')->references('id')->on('board');
            $table->unsignedBigInteger('medium_id')->nullable();
            $table->foreign('medium_id')->references('id')->on('medium');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->foreign('class_id')->references('id')->on('class');
            $table->integer('standard_id')->nullable();
            $table->unsignedBigInteger('stream_id')->nullable();
            $table->foreign('stream_id')->references('id')->on('stream');
            $table->string('subject_id')->nullable();
            $table->enum('status', ['0', '1', '2']);
            $table->string('note')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_detail');
    }
};
