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
        Schema::table('users_sub_experience', function (Blueprint $table) {
            $table->dropColumn('startdate');
            $table->dropColumn('enddate');
            $table->string('experience')->after('institute_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_sub_experience', function (Blueprint $table) {
            $table->dropColumn('startdate');
            $table->dropColumn('enddate');
            $table->string('experience')->after('institute_name')->nullable();
        });
    }
};
