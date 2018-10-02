<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBox extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user');
            $table->longText('content');
            $table->string('uri', 100)->unique();
            $table->string('config');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            // indexes
            $table->index('uri');
            $table->index(['user', 'created_at']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boxes');
        //
    }
}
