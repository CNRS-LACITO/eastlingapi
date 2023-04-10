<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned();
            $table->enum('type', ['T','S', 'W', 'M']);
            $table->integer('rank');
            $table->integer('image_id')->nullable()->unsigned();
            $table->string('areaCoords')->nullable();
            $table->string('audioStart')->nullable();
            $table->string('audioEnd')->nullable();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->json('imageCoords')->nullable(); //for multiselection
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::table('annotations', function($table) {
           $table->foreign('document_id')->references('id')->on('documents');
           $table->foreign('parent_id')->nullable()->references('id')->on('annotations')->onDelete('cascade');;
           //$table->foreign('image_id')->nullable()->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('annotations');
    }
}
