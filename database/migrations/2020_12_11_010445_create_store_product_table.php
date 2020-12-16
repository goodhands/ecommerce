<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable(); // connect with store
            $table->string('name');
            $table->string('shortname');
            $table->float('price')->default(0.00);
            $table->float('discount')->default(0.00);
            $table->text('description')->nullable();
            $table->string('sku')->nullable();
            $table->integer('stock')->default(1);
            //delivery: id => 
            // $table->string('shipping_provider')->nullable();
            $table->string('product_type')->default('physical'); //digital or physical: only physical is supported for now
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('collection_id')->nullable(); // e.g: Men's wear -> defaults to home
            $table->unsignedBigInteger('category_id')->nullable(); // product type/category, e.g: Shirt
            //{cover:'image.png', }
            $table->json('media_library')->nullable(); //media library, first one will be picked as featured
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
        Schema::dropIfExists('store_product');
    }
}
