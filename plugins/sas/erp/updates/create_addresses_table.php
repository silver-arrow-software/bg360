<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class SasCreateAddressesTable extends Migration {
    public function up() {
        Schema::create('sas_erp_addresses', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('street_addr', 200)->nullable();
            $table->string('district', 50)->nullable();
            $table->integer('state_id')->unsigned()->nullable()->index();
            $table->integer('country_id')->unsigned()->nullable()->index();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('sas_erp_addresses');
    }
}