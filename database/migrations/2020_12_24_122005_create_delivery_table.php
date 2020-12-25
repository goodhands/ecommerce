<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliverys', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //Standard
            $table->string('description');
            $table->string('label'); //unique id to match config
            $table->string('website')->nullable(); //website for integration apps
            $table->string('rates')->nullable(); //percentage rate on each transaction. only for 3rd parties
            $table->string('type')->default('manual'); //manual or 3rd party
            $table->timestamps();
        });

        Schema::create('delivery_store', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id'); //1
            $table->unsignedBigInteger('delivery_id'); //1
            $table->float('flat_rate')->default(0.00); //base fee to charge, free is 0.00
            //if any value higher than 1, we will only 
            //apply the flat_rate to orders higher than the value
            $table->float('conditional_pricing')->default(0.00);
            $table->tinyInteger('active')->default('1');
            $table->string('notes')->nullable(); //delivery information
            $table->timestamps();
        });

        Schema::create('delivery_region', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('delivery_id');        
            $table->string('location'); //we should store the longitude and latitude like: 44.4647452,7.3553838    
            $table->float('price')->default(0.00); //if there is a special price for a location, add it to the flat rate
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
        Schema::dropIfExists('delivery');
    }
}
