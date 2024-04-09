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
            $table->dropForeign(['role_type']);
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->string('role_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreign('role_type')->references('id')->on('roles');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->unsignedBigInteger('role_type')->change();
        });
    }
};
