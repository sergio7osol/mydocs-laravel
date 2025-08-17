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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 70);
            $table->string('filename', 50);
            $table->string('file_path', 255);
            $table->integer('file_size');
            $table->string('file_type', 50);
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->date('created_date')->nullable();
            $table->mediumText('description')->nullable();
            $table->foreignIdFor(\App\Models\Category::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
