<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_seed_imports', function (Blueprint $table) {
            $table->id();
            $table->string('collection', 32);
            $table->string('source_id', 64);
            // Deliberately retain the import history after permanent content deletion.
            $table->string('entry_id', 64);
            $table->json('media_keys')->nullable();
            $table->timestamps();
            $table->unique(['collection', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_seed_imports');
    }
};
