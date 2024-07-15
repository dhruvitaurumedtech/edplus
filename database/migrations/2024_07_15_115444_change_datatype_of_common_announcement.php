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
        Schema::table('common_announcement', function (Blueprint $table) {
            $table->text('announcement')->change();
            $table->text('institute_id')->change();
            $table->text('teacher_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('common_announcement', function (Blueprint $table) {
            $table->string('announcement')->change();
            $table->string('institute_id')->change();
            $table->string('teacher_id')->change();
        });
    }
};
