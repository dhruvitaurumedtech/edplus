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
        Schema::table('students_details', function (Blueprint $table) {
            $table->string('pincode')->nullable()->after('note');
            $table->string('city')->nullable()->after('note');
            $table->string('state')->nullable()->after('note');
            $table->string('country')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students_details', function (Blueprint $table) {
            //
        });
    }
};
