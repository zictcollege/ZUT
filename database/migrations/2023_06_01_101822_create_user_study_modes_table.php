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
        Schema::create('ac_userModes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('studyModeID');
            $table->unsignedInteger('userID');
            $table->timestamps();

//            $table->foreign('studyModeID')->references('id')->on('ac_studyModes')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
//
        });
    }

    public function down()
    {
        Schema::dropIfExists('ac_userModes');
    }
};
