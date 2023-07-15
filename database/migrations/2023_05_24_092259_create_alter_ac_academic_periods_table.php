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
        Schema::create('ac_academicPeriods', function (Blueprint $table) {
            $table->id();
            $table->String('code',100)->unique();
            $table->date('registrationDate');
            $table->date('lateRegistrationDate');
            $table->date('acStartDate');
            $table->date('acEndDate');
            $table->integer('periodID');
            $table->unsignedBigInteger('type');
            $table->unsignedInteger('studyModeIDAllowed');
            $table->integer('registrationThreshold');
            $table->integer('resultsThreshold');
            $table->integer('examSlipThreshold');
            $table->timestamps();

//            $table->foreign('type')
//                ->references('id')
//                ->on('ac_periodTypes')
//                ->onDelete('restrict');
//
//            $table->foreign('studyModelDAllowed')
//                ->references('id')
//                ->on('ac_studyModes')
//                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_academicPeriods');
    }
};
