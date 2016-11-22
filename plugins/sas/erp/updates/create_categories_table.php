<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SasCreateCategoriesTable extends Migration {
    public function up() {
        Schema::create('sas_erp_categories', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title')->index();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->timestamps();
        });

        Schema::create('sas_erp_products_categories', function($table) {
            $table->engine = 'InnoDB';
            $table->integer('product_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['product_id', 'category_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('sas_erp_categories');
        Schema::dropIfExists('sas_erp_products_categories');
    }
}