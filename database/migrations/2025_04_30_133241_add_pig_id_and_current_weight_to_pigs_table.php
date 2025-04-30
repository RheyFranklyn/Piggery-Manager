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
        Schema::table('pigs', function (Blueprint $table) {
            $table->string('pig_id')->unique()->nullable()->after('id'); // or use ->unique() if needed
            $table->string('current_weight')->nullable()->after('starting_weight'); // or ->float(), depending on how you store weight
        });
    }

    public function down()
    {
        Schema::table('pigs', function (Blueprint $table) {
            $table->dropColumn('pig_id');
            $table->dropColumn('current_weight');
        });
    }
};
