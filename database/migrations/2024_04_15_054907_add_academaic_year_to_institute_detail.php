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
        Schema::table('institute_detail', function (Blueprint $table) {
            $table->string('end_academic_year')->nullable()->after('status');
            $table->string('start_academic_year')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institute_detail', function (Blueprint $table) {
            //
        });
    }
};
