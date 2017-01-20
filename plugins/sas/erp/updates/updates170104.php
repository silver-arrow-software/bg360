<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Updates170104 extends Migration {
    public function up() {
        Schema::create('sas_erp_project_columns', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->string('name')->nullable();
            $table->smallInteger('position');
            $table->timestamps();
        });

        Schema::table('sas_erp_tasks', function ($table) {
            $table->integer('column_id')->unsigned();
            $table->smallInteger('position');
        });
    }

    public function down() {
        Schema::dropIfExists('sas_erp_project_columns');

        Schema::table('sas_erp_tasks', function ($table) {
            $table->dropColumn(['column_id', 'position']);
        });
    }
}
