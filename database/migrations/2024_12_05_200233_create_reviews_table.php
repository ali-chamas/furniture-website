<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();  // Default primary key 'id'
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Foreign key to users table
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');  // Foreign key to products table
            $table->integer('rating');
            $table->text('review_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
