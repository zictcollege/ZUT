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
        Schema::create('ac_academicPeriodFees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('academicPeriodID');
            $table->unsignedInteger('studyModeID');
            $table->unsignedInteger('feeID');
            $table->text('amount');
            $table->unsignedInteger('added_by_id');
            $table->boolean('published')->default(0);
            $table->unsignedInteger('published_by')->nullable();
            $table->string('key', 255)->unique();
            $table->boolean('once_off')->default(0);
            $table->boolean('crf')->default(0);
            $table->boolean('p_f')->default(0);
            $table->unsignedInteger('class_id')->nullable();
            $table->unsignedInteger('hostel_fee')->nullable();
            $table->timestamps();

//            $table->foreign('academicPeriodID')->references('id')->on('ac_academicPeriods')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('studyModeID')->references('id')->on('ac_studyModes')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('feeID')->references('id')->on('ac_fees')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ac_academicPeriodFees');
    }
};
