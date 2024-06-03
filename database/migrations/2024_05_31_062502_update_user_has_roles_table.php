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
        Schema::table('user_has_roles', function (Blueprint $table) {
            $table->dropForeign(['institute_id']);
            $table->dropColumn('institute_id');

            $table->unsignedBigInteger('user_id')->after('role_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_has_roles', function (Blueprint $table) {
            // Add the institute_id column and set up the foreign key constraint
            $table->unsignedBigInteger('institute_id')->after('role_id');
            $table->foreign('institute_id')->references('id')->on('institute_detail');

            // Remove the foreign key and the column for user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
