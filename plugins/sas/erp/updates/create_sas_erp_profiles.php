<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SasCreateSasErpProfiles extends Migration
{
    public function up()
    {
        Schema::create('sas_erp_profiles', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 100);
            $table->text('description')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('sas_erp_profiles');
    }
}