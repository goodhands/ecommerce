<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('shortname');
            $table->string('industry')->nullable();
            $table->string('size')->nullable();
            $table->string('category')->nullable();

            $table->string('current_billing_plan')->nullable();
            $table->string('card_type')->nullable();
            $table->string('last4')->nullable();
            $table->string('bank')->nullable();
            $table->string('channel')->nullable();
            $table->string('exp_month')->nullable();
            $table->string('exp_year')->nullable();
            $table->string('billing_address')->nullable();
            $table->text('authorization_code')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
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
        Schema::dropIfExists('store');
    }
}
