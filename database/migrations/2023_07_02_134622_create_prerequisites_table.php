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
        Schema::create('ac_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courseID');
            $table->unsignedBigInteger('prerequisiteID');
            $table->timestamps();
            $table->string('key')->nullable();

//            $table->foreign('courseID')->references('id')->on('ac_courses')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('prerequisiteID')->references('id')->on('ac_courses')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_prerequisites');
    }
};
