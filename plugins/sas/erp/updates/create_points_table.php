<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class SasCreatePointsTable extends Migration
{
    public function up() {
        Schema::create('sas_erp_points', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description');
			$table->integer('amount');
            $table->timestamps();
        });

        Schema::create('sas_erp_points_users', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('point_id')->unsigned();
            $table->text('notes');
            $table->timestamps();
        });
	}

    public function down() {
        Schema::dropIfExists('sas_erp_points');
        Schema::dropIfExists('sas_erp_points_users');
    }
}
