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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pig_id')->nullable()->constrained("pigs")->onDelete('set null'); // for per pig expense 
            $table->foreignId('batch_id')->nullable()->constrained("batches")->onDelete('set null'); // for group/batch expenses
            $table->string('category'); // Feed, Medicine, Repairs, etc.
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly'])->nullable(); // daily, weekly, monthly
            $table->string('receipt_path')->nullable(); // for uploaded image path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
