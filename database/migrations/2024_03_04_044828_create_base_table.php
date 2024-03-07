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
        Schema::create('base_table', function (Blueprint $table) {
            $table->id();
            $table->integer('institute_for');
            $table->integer('board')->nullable();
            $table->integer('medium');
            $table->integer('institute_for_class');
            $table->integer('standard');
            $table->integer('stream')->nullable();
            $table->enum('status',['active','inactive']);
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
        Schema::dropIfExists('base_table');
    }
};
