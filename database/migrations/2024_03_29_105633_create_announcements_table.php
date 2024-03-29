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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('institute_id');
            $table->foreign('institute_id')->references('id')->on('institute_detail');
            $table->unsignedBigInteger('board_id');
            $table->foreign('board_id')->references('id')->on('board');
            $table->unsignedBigInteger('medium_id');
            $table->foreign('medium_id')->references('id')->on('medium');
            $table->unsignedBigInteger('institute_for_id');
            $table->foreign('institute_for_id')->references('id')->on('institute_for');
            $table->unsignedBigInteger('standard_id');
            $table->foreign('standard_id')->references('id')->on('standard');
            $table->unsignedBigInteger('stream_id')->nullable();
            $table->foreign('stream_id')->references('id')->on('stream');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subject');
            $table->unsignedBigInteger('role_type')->nullable();
            $table->foreign('role_type')->references('id')->on('roles');
            $table->string('title');
            $table->text('detail');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
