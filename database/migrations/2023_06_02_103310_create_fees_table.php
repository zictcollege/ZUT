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
        Schema::create('ac_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->string('chart_of_account_id', 255);
            $table->integer('archieved')->default(0);
            $table->timestamps();
        });
    }

        public function down()
    {
        Schema::dropIfExists('ac_fees');
    }
};
