<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class SasCreateProjectsTable extends Migration
{
    public function up() {
        Schema::create('sas_erp_teams', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
			$table->integer('place_id')->unsigned();
            $table->timestamps();
        }); 

        Schema::create('sas_erp_teams_users', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
			$table->integer('team_id')->unsigned();
			$table->boolean('lead')->default(false);
			$table->primary(['user_id', 'team_id']);
        }); 

        Schema::create('sas_erp_projects', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description');
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->integer('team_id')->unsigned();
			$table->integer('place_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('sas_erp_tasks', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100);
            $table->text('description');
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->integer('project_id')->unsigned();
			$table->smallInteger('status')->nullable();
            $table->timestamps();
        });

		Schema::create('sas_erp_tasks_users', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
			$table->integer('task_id')->unsigned();
			$table->primary(['user_id', 'task_id']);
        }); 

		Schema::create('sas_erp_tasks_todos', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
			$table->integer('task_id')->unsigned();
			$table->text('description')->nullable();
			$table->smallInteger('status')->nullable();
            $table->timestamps();
        }); 
}

    public function down() {
        Schema::dropIfExists('sas_erp_teams');
        Schema::dropIfExists('sas_erp_teams_users');
        Schema::dropIfExists('sas_erp_projects');
        Schema::dropIfExists('sas_erp_tasks');
        Schema::dropIfExists('sas_erp_tasks_todos');
        Schema::dropIfExists('sas_erp_tasks_users');
    }
}
