<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lang');
            $table->enum('type', ['TEXT', 'WORDLIST']);
            $table->integer('user_id')->unsigned();
            $table->timestamp('recording_date', $precision = 0)->nullable();
            $table->string('recording_place')->nullable();
            $table->json('available_kindOf')->nullable();
            $table->json('available_lang')->nullable();
            $table->string('oai_primary');
            $table->string('oai_secondary');
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });


        Schema::table('documents', function($table) {
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
        Schema::dropIfExists('documents');
    }
}
