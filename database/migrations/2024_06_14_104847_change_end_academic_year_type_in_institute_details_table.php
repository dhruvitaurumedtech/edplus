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
            // Change the datatype of the 'end_academic_year' column to DATE
            $table->date('start_academic_year')->nullable()->change();
            $table->date('end_academic_year')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institute_detail', function (Blueprint $table) {
            $table->string('start_academic_year')->change();
            $table->string('end_academic_year')->change();
        });
    }
};
