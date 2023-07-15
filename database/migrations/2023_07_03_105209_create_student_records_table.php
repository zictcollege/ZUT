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
        Schema::create('ac_student_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('userID');
            $table->unsignedInteger('programID');
            $table->unsignedInteger('intakeID');
            $table->unsignedInteger('studymodeID');
            $table->unsignedInteger('level_id');
            $table->unsignedInteger('typeID');
            $table->integer('grad')->default(0);
            $table->string('sessions');
            $table->date('year_admitted');
            $table->timestamps();

//            $table->foreign('programID')->references('id')->on('ac_programs')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('intakeID')->references('id')->on('ac_program_intakes')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('level_id')->references('id')->on('ac_course_levels')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('studymodeID')->references('id')->on('ac_studyModes')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('typeID')->references('id')->on('ac_periodTypes')->onDelete('restrict')->onUpdate('restrict');
//            $table->foreign('userID')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_records');
    }
};
