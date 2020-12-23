<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('label'); //unique id to match config
            $table->string('website')->nullable();
            $table->string('rates')->nullable(); //percentage rate on each transaction. only for 3rd parties
            $table->string('type')->default('manual'); //manual or 3rd party
            $table->timestamps();
        });

        Schema::create('payment_store', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('payment_id');
            $table->string('notes')->nullable();
            $table->tinyInteger('active')->default(1); //active by default
            $table->json('channels')->nullable(); //payment channels for 3rd party
            $table->timestamps();

            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade');

            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
