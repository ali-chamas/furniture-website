<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();  // Default primary key 'id'
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');   // Foreign key to products table
            $table->string('image_url');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
