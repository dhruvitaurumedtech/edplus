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
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['institute_for_id']);
        });

        // Make column nullable
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('institute_for_id')->nullable()->change();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreign('institute_for_id')->references('id')->on('institute_for');
        });

        // Make column not nullable
        Schema::table('announcements', function (Blueprint $table) {
            $table->integer('institute_for_id')->nullable(false)->change();
        });
    }
};
