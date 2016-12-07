<?php namespace Sas\Social\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sas_social_messages_threads', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('subject');
            $table->string('slug')->index();
            $table->softDeletes();
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
        Schema::dropIfExists('sas_social_messages_threads');
    }

}