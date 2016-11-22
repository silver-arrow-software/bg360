<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SasCreateProductsTable extends Migration {

    public function up() {
        Schema::create('sas_erp_products', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('place_id')->unsigned()->nullable()->index();
            $table->string('title')->index()->nullable();
            $table->string('slug')->index()->unique();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('published')->default(true);
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
		
		Schema::create('sas_erp_product_attributes', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->unsigned()->nullable()->index();
            $table->string('name')->nullabel();
            $table->text('value')->nullable();
            $table->timestamps();
        });

		Schema::create('sas_erp_product_items', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->unsigned()->nullable()->index();
			$table->integer('inventory_id')->unsigned()->nullable();
			$table->string('code', 20)->nullable();
            $table->decimal('price', 10, 0)->default(0); 
			$table->integer('quantity')->unsigned()->default(0);
            $table->timestamps();
        });
	}

    public function down() {
        Schema::dropIfExists('sas_erp_products');
        Schema::dropIfExists('sas_erp_product_attributes');
        Schema::dropIfExists('sas_erp_product_items');
    }

}