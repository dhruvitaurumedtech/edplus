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
        Schema::create('subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('standard_id');
            $table->foreign('standard_id')->references('id')->on('standard');
            $table->unsignedBigInteger('stream_id');
            $table->foreign('stream_id')->references('id')->on('stream');
            $table->string('name');
            $table->enum('status',['active','inactive']);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject');
    }
};
