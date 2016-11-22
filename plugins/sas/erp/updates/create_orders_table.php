<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SasCreateOrdersTable extends Migration {

    public function up() {
        Schema::create('sas_erp_orders', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('email')->nullable();
            $table->text('items');
            $table->text('billing_info')->nullable();
            $table->text('shipping_info')->nullable();
            $table->decimal('vat', 7, 0);
            $table->decimal('total', 7, 0);
            $table->string('currency');
            $table->integer('status')->unsigned()->default(1);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down() {
        Schema::dropIfExists('sas_erp_orders');
    }

}