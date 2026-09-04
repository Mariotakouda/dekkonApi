<?php

use App\Enums\CategoryAttributeType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('type')->default(CategoryAttributeType::TEXT->value);
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_variant_attribute')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'key']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_attributes');
    }
};
