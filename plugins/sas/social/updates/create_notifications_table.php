<?php namespace Sas\Social\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sas_social_notifications', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type');
            $table->morphs('sender');
            $table->morphs('object');
            $table->timestamps();
        });

        Schema::create('sas_social_notifications_users', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('notification_id')->unsigned()->index();
            $table->timestamp('read_at')->nullable();
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
        Schema::dropIfExists('sas_social_notifications');
        Schema::dropIfExists('sas_social_notifications_users');
    }
}