<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['professional', 'cultural', 'corporate'])->default('professional');
            $table->dateTime('date');
            $table->string('location');
            $table->integer('capacity');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('cover_image')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft');
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};