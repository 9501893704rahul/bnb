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
        Schema::table('room_photos', function (Blueprint $table) {
            // Path to high-resolution original photo
            $table->string('high_res_path', 2048)->nullable()->after('path');
            // Path to thumbnail with timestamp overlay for web display
            $table->string('thumbnail_path', 2048)->nullable()->after('high_res_path');
            // Photo type: 'completion' for after-task photos, 'problem' for issue photos
            $table->enum('photo_type', ['completion', 'problem'])->default('completion')->after('thumbnail_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_photos', function (Blueprint $table) {
            $table->dropColumn(['high_res_path', 'thumbnail_path', 'photo_type']);
        });
    }
};
