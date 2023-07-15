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
        Schema::create('ac_programCourses', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('level_id');
            $table->unsignedInteger('courseID');
            $table->unsignedInteger('programID');
            $table->string('key')->collation('utf8mb4_unicode_ci');
            $table->integer('active')->default(1);
            $table->integer('programIntakeID')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

//            $table->foreign('courseID')->references('id')->on('ac_courses')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('programID')->references('id')->on('ac_programs')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('level_id')->references('id')->on('ac_course_levels')->onDelete('restrict')->onUpdate('restrict');
//
        });
    }

        public function down()
    {
        Schema::dropIfExists('ac_programCourses');
    }
};
