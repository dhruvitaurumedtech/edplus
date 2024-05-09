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
        Schema::table('topic', function (Blueprint $table) {
            //
            $table->integer('updated_by')->nullable()->after('status');
            $table->integer('created_by')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topic', function (Blueprint $table) {
            //
        });
    }
};
