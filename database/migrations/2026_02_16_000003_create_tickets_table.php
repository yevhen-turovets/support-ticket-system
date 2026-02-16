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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->enum('category', ['Billing', 'Technical', 'Account', 'General'])->nullable();
            $table->enum('sentiment', ['Positive', 'Neutral', 'Negative'])->nullable();
            $table->text('suggested_reply')->nullable();
            $table->enum('urgency', ['Low', 'Medium', 'High', 'Critical'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
