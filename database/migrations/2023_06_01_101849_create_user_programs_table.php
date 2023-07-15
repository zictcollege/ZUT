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
        Schema::create('ac_userPrograms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('programID');
            $table->unsignedInteger('userID');
            $table->timestamps();
//
//            $table->foreign('programID')->references('id')->on('ac_programs')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
//
       });
    }

    public function down()
    {
        Schema::dropIfExists('ac_userPrograms');
    }
};
