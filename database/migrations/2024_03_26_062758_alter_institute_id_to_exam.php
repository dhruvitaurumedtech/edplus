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
        Schema::table('exam', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_id')->after('id');
            $table->unsignedBigInteger('user_id')->after('institute_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam', function (Blueprint $table) {
            //
        });
    }
};
