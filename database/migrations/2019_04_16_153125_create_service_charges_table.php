<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('service_type');
            $table->dateTime('renewal_date');
            $table->double('charge_amount');
            $table->integer('is_service_active');
            $table->timestamps();
        });
    }
	public function down()
    {
        Schema::dropIfExists('service_charges');
    }
}
