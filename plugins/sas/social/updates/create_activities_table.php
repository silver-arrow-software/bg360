<?php namespace Sas\Social\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sas_social_activities', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('subject_id')->index();
            $table->string('subject_type')->index();
            $table->integer('user_id')->index();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sas_social_activities');
    }

}