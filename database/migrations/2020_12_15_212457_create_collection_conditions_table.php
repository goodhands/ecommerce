<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('field'); //match with a value -> product title
            $table->string('value'); //value to match -> "beared cream"
            $table->string('condition'); //condition -> "equal to"|"not equal to"|"greater than"|"less than"
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
        Schema::dropIfExists('collection_conditions');
    }
}
