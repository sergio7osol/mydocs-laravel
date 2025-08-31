<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Document;
use App\Models\Label;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_label', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Document::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Label::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_label');
    }
};
