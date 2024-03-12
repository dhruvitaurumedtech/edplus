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
        Schema::create('topic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_table_id');
            $table->foreign('base_table_id')->references('id')->on('base_table');
            $table->unsignedBigInteger('standard_id');
            $table->foreign('standard_id')->references('id')->on('standard');
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subject');
            $table->unsignedBigInteger('chapter_id');
            $table->foreign('chapter_id')->references('id')->on('chapters');
            $table->unsignedBigInteger('video_category_id');
            $table->foreign('video_category_id')->references('id')->on('video_categories');
            $table->string('topic_no');
            $table->string('topic_name');
            $table->string('topic_video');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic');
    }
};
