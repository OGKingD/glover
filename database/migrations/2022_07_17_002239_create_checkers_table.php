<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateCheckersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkers', function (Blueprint $table) {
            $table->id();
            $table->uuid('ref')->default(Str::uuid());
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('maker_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('maker_id')->references('id')->on('makers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checker');
    }
}
