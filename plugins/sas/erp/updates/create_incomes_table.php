<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('sas_erp_incomes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sas_erp_incomes');
    }
}
