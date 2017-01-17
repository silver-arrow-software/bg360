<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Updates161222 extends Migration {
    public function up() {
        Schema::table('sas_erp_products', function ($table) {
            $table->decimal('price', 10, 0)->default(0);
            $table->integer('quantity')->unsigned()->default(0);
            $table->string('hash_id');
        });

        Schema::create('sas_erp_users_places', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('place_id')->unsigned();
            $table->text('params');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::table('sas_erp_products', function ($table) {
            $table->dropColumn(['price', 'quantity', 'hash_id']);
        });

        Schema::dropIfExists('sas_erp_users_places');
    }
}
