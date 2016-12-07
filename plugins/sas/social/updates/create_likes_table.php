<?php namespace Sas\Social\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sas_social_likes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('likeable_id');
            $table->string('likeable_type');
            $table->timestamps();
            $table->index(['likeable_id', 'likeable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sas_social_likes');
    }

}
