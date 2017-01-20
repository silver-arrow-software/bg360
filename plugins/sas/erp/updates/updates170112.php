<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Updates170112 extends Migration {
    public function up() {
        Schema::table('sas_erp_products', function ($table) {
            $table->dropColumn('place_id');
            $table->json('options')->nullable();
        });

        Schema::table('sas_erp_product_items', function ($table) {
            $table->integer('place_id')->unsigned();
            $table->longText('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down() {
        Schema::table('sas_erp_product_items', function ($table) {
            $table->dropColumn(['place_id', 'description', 'status', 'deleted_at']);
        });
        Schema::table('sas_erp_products', function ($table) {
            $table->dropColumn(['options']);
        });
    }
}
