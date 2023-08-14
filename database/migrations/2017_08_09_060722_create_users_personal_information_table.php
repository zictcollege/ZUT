<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPersonalInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_personal_information');
    }
}


    