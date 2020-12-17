<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityToStoreOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_order', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id');
            $table->float('total'); //total amount of money
            $table->unsignedBigInteger('store_id');
            $table->dropColumn('product_id')->change(); //we don't need this as we will have a pivot table
            $table->string('payment_status'); //paid, pending (in the case of cash on delivery) 
            $table->tinyInteger('fulfilled');   //whether or not the product has been delivered
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
