<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SasCreateProjectColumns extends Migration {
    public function up() {
        Schema::create('sas_erp_project_columns', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->string('name')->nullable();
            $table->smallInteger('linked_status');
            $table->decimal('position',24,15);
            $table->timestamps();
        });

        Schema::table('sas_erp_tasks', function ($table) {
            $table->integer('column_id')->unsigned();
            $table->decimal('position',24,15);
        });
    }

    public function down() {
        Schema::dropIfExists('sas_erp_project_columns');

        Schema::table('sas_erp_tasks', function ($table) {
            $table->dropColumn(['column_id', 'position']);
        });
    }
}
