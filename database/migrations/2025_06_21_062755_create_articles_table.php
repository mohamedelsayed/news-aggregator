<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('url')->unique();          // Unique to avoid duplicates
            $table->text('image_url')->nullable();
            $table->string('source');                 // e.g. NewsAPI, Guardian, NewYorkTimes
            $table->string('author')->nullable();
            $table->string('category')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();                     // created_at + updated_at
        });

        DB::statement('ALTER TABLE articles ADD FULLTEXT fulltext_index (title, description, content)');

        Schema::table('articles', function (Blueprint $table) {
            $table->index('source');
            $table->index('category');
            $table->index('published_at');
            $table->index('author');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
