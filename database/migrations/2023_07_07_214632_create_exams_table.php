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
        Schema::create('ac_gradeBooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID');
            $table->integer('grade');
            $table->unsignedBigInteger('classAssessmentID');
            $table->timestamps();
            $table->string('key')->nullable()->unique();
            $table->integer('gradeType')->default(1);

//            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('classAssessmentID')->references('id')->on('ac_classAssessments')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_gradeBooks');
    }
};
