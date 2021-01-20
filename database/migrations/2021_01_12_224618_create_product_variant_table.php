<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantTable extends Migration
{

    /**
        variant:{
            color:{
                blue: {
                    price: 10,
                    stock: 2
                }
            }
        }

        variant:{
            0:{
                price: 1000,
                stock: 2,
                variations:{
                    color: Red,
                    size: Blue
                }
            },
            1:{

            }
        } 
    */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id'); //Size
            $table->string('variant'); //MD | SM | XL | LG
            $table->string('stock'); //12
            $table->float('price'); //15,633.00
            $table->json('media');
            $table->timestamps();
        });

        Schema::create('variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type'); //size | color | length | weight
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
        Schema::dropIfExists('product_variant');
    }
}
