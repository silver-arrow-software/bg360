<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SasCreatePlacesTable extends Migration
{
    public function up()
    {
        Schema::create('sas_erp_places', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('code_id')->index()->unique();
            $table->string('street_addr', 100)->nullable();
            $table->string('district', 50)->nullable();
            $table->integer('state_id')->unsigned()->nullable()->index();
            $table->integer('country_id')->unsigned()->nullable()->index();
            $table->string('phone', 11)->nullable();
            $table->integer('created_by')->unsigned();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('sas_erp_places');
    }
}