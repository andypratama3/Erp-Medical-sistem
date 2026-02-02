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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // User receiving the notification
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            
            // Notification details
            $table->string('type', 50); // e.g., 'sales_do_submitted', 'delivery_completed'
            $table->string('title', 255);
            $table->text('message')->nullable();
            $table->string('url', 500)->nullable(); // Link to the related resource
            
            // Additional data (JSON)
            $table->json('data')->nullable(); // Store related IDs, etc.
            
            // Read status
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('type');
            $table->index('read_at');
            $table->index(['user_id', 'read_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
