<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**==========================================================
         * Here we will manage the stock level of every product.
         * Each time a product is sold, we will register the quantity
         * sold here and when we need to find the stock remaining,
         * we SUM all the [sold] actions of the product and then 
         * deduct from the original quantity on the product table.
         * ===========================================================
         * And when a product is restocked, we will add it here as well
         */
        Schema::create('store_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id'); //1
            $table->unsignedBigInteger('product_id'); //1
            $table->unsignedBigInteger('variant_id')->nullable(); //1
            $table->unsignedBigInteger('quantity'); // 1
            $table->string('action'); //stock (add new stock) | sale (reduce stock)
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
        Schema::dropIfExists('store_inventory');
    }
}
