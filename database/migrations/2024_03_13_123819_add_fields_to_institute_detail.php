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
            $table->string('youtube_link')->nullable()->after('email');
            $table->string('whatsaap_link')->nullable()->after('email');
            $table->string('facebook_link')->nullable()->after('email');
            $table->string('instagram_link')->nullable()->after('email');
            $table->string('website_link')->nullable()->after('email');
            $table->string('gst_slab')->nullable()->after('email');
            $table->string('gst_number')->nullable()->after('email');
            $table->string('close_time')->nullable()->after('email');
            $table->string('open_time')->nullable()->after('email');
            $table->string('logo')->nullable()->after('email');
             
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
