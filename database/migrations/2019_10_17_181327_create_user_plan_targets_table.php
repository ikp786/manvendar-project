<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPlanTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plan_targets', function (Blueprint $table) 
		{
            $table->increments('id');
            $table->integer('userplan_id');
            $table->double('dmt_target');
			$table->string('dtm_scheme');
			$table->timestamp('dmt_traget_expire_date')->nullable();
			$table->string('recharge_scheme');
            $table->double('recharge_target');
			$table->timestamp('recharge_traget_expire_date')->nullable();
            $table->double('bill_target');
			$table->string('bill_scheme');
			$table->timestamp('bill_traget_expire_date')->nullable();
			$table->text('remark')->nullable();
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
        Schema::dropIfExists('user_plan_targets');
    }
}
