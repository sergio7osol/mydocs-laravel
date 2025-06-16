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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('parent_id')->nullable()->constrained('categories');
            $table->string('path', 255)->nullable();
            $table->integer('level')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Indexes for hierarchical queries
            $table->index('level');
            $table->unique(['name', 'parent_id'], 'unique_name_parent');
            $table->index('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
