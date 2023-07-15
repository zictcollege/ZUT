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
        Schema::create('ac_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('instructorID');
            $table->unsignedInteger('roomID')->default(0);
            $table->unsignedInteger('courseID');
            $table->unsignedInteger('academicPeriodID');
            $table->integer('boeMark')->default(0);
            $table->tinyInteger('boeAssesed')->default(0);
            $table->tinyInteger('boePublished')->default(0);
            $table->timestamps();
            $table->integer('key');

            $table->unique('key', 'ac_classes_key_unique');
            $table->index('instructorID', 'ac_classes_instructorid_foreign');
            $table->index('courseID', 'ac_classes_courseid_foreign');
            $table->index('academicPeriodID', 'ac_classes_academicperiodid_foreign');

//            $table->foreign('instructorID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('courseID')->references('id')->on('ac_courses')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('academicPeriodID')->references('id')->on('ac_academicPeriods')->onDelete('cascade')->onUpdate('cascade');
//

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ac_classes');
    }
};
