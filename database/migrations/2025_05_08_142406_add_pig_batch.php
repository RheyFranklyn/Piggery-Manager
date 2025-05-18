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
        Schema::table('pigs', function (Blueprint $table) {
            // Add batch_id column after 'current_weight' and reference the 'batches' table
            $table->foreignId('batch_id')
                ->after('current_weight')
                ->nullable()
                ->constrained('batches')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pigs', function (Blueprint $table) {
            // Drop the foreign key and the column
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};
