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
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->date('published_date')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image', 2048)->nullable();
            $table->string('pdf_path', 2048);
            $table->unsignedBigInteger('pages')->nullable();
            $table->unsignedBigInteger('pdf_size')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
