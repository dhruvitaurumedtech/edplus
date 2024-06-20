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
            $table->text('country_code_name')->nullable()->after('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institute_detail', function (Blueprint $table) {
            $table->dropColumn('country_code_name');
        });
    }
};
