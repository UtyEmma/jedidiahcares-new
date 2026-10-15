<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->json('values');
            $table->longText('header_scripts')->nullable();
            $table->longText('footer_scripts')->nullable();
            $table->text('robots_txt')->nullable();
            $table->boolean('scripts_enabled')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Remove the original launch-time indexing block for existing installations.
        DB::table('seo_settings')->update(['indexing_enabled' => null, 'indexable' => true]);
        DB::table('seo_settings')->where('path', 'global')->update(['indexing_enabled' => true]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
