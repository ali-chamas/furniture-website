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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();  // Default primary key 'id'
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Foreign key to users table
            $table->timestamp('order_date');
            $table->foreignId('status_id')->constrained('order_status')->onDelete('cascade'); // Foreign key to order_status table
            $table->decimal('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
