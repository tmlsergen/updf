<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clouds', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->longtext('content');
            $table->text('options')->nullable();
            $table->string('html')->nullable();
            $table->string('pdf')->nullable();
            $table->string('svg')->nullable();
            $table->string('png')->nullable();
            $table->string('jpg')->nullable();
            $table->string('spng')->nullable();
            $table->string('sjpg')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clouds');
    }
}
