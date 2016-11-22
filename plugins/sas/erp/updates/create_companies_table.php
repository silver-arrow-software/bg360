<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SasCreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('sas_erp_companies', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('type_id')->unsigned()->default(2);
            $table->string('name', 100);
            $table->string('name_en', 100);
            $table->text('description')->nullable();
            $table->string('tax_id')->unique();
            $table->string('street_addr', 200)->nullable();
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
        Schema::dropIfExists('sas_erp_companies');
    }
}