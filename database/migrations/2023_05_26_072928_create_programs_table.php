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
        Schema::create('ac_programs', function (Blueprint $table) {
            $table->id();
            $table->text('code');
            $table->text('name');
            $table->unsignedBigInteger('departmentID');
            $table->integer('qualification_id');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('departmentID')->references('id')->on('ac_departments')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ac_programs');
    }
};
