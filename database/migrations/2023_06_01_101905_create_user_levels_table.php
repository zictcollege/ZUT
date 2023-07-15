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
        Schema::create('ac_user_course_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('levelID');
            $table->unsignedInteger('userID');
            $table->timestamps();

//            $table->foreign('levelID')->references('id')->on('ac_course_levels')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
//
        });
    }

    public function down()
    {
        Schema::dropIfExists('ac_course_levels');
    }
};
