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
        Schema::create('ac_classAssessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessmentID');
            $table->unsignedBigInteger('classID');
            $table->text('total');
            $table->timestamps();
            $table->string('key')->nullable();

//            $table->foreign('assessmentID')->references('id')->on('ac_assessmentTypes')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('classID')->references('id')->on('ac_classes')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_classAssessments');
    }
};
