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
        Schema::create('pigs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pen_number'); // snake_case is Laravel convention
            $table->date('date_bought');
            $table->decimal('cost', 10, 2); // to handle prices accurately
            $table->string('breed')->nullable(); // breed is optional in your form
            $table->date('expected_sell_date');
            $table->string('starting_weight'); // includes "kg", so keep as string

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pigs');
    }
};
