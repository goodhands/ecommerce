<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableTypesToStoreOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_order', function (Blueprint $table) {
            $table->float('total')->nullable()->change(); //this will be calculated with a job
            $table->unsignedBigInteger('store_id')->nullable()->change(); //so we can use the save() method
            $table->string('payment_status')->default('Pending')->change(); //pending until we receive a webhook from the provider 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_order', function (Blueprint $table) {
            //
        });
    }
}
