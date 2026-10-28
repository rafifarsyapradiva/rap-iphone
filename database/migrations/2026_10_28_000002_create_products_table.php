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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('series', 50);
            $table->enum('condition', ['new', 'second'])->default('new');
            $table->string('grade', 20)->nullable();
            $table->enum('warranty_type', ['resmi_ibox', 'resmi_digimap', 'inter_all_operator', 'toko'])->default('resmi_ibox');
            $table->decimal('base_price', 12, 2);
            $table->longText('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
