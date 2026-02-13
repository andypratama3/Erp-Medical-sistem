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
        Schema::create('email_companies', function (Blueprint $table) {
            $table->id();
            $table->string('scope_type')->default('department');
            $table->string('dept_code')->nullable();
            $table->string('office_code')->nullable();
            $table->string('email_local');
            $table->string('email_domain')->default('rizqullahmediska.com');
            $table->string('email_full');
            $table->string('note')->nullable();
            $table->tinyInteger('is_primary')->default(1);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_companies');
    }
};
