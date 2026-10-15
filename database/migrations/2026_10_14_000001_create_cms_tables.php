<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('editor');
            $table->boolean('is_active')->default(false);
        });
        Schema::create('content_entries', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('collection', 32)->index();
            $table->string('slug');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('data')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['collection', 'slug']);
            $table->index(['collection', 'status', 'sort_order']);
        });
        Schema::create('content_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('collection', 32);
            $table->string('slug');
            $table->string('entry_id', 64);
            $table->foreign('entry_id')->references('id')->on('content_entries')->cascadeOnDelete();
            $table->unique(['collection', 'slug']);
        });
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->string('model_id', 64);
            $table->index(['model_type', 'model_id']);
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->nullableTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('content_redirects');
        Schema::dropIfExists('content_entries');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'is_active']));
    }
};
