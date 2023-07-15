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
        Schema::create('users_personal_information', function(Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->date('dob');
            $table->string('marital_status');
            $table->string('street_main');
            $table->integer('post_code')->nullable();
            $table->string('province_state');
            $table->string('town_city');
            $table->string('telephone')->nullable();
            $table->string('mobile');
            $table->string('nationality');
            $table->string('country_of_residence');
            $table->string('nrc');
            $table->string('passport_number')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_personal_information');
    }
};
