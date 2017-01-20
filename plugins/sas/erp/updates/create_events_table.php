<?php namespace Sas\Erp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class SasCreateEventsTable extends Migration {

    public function up() {
        Schema::create('sas_erp_events', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('created_by')->unsigned();
            $table->string('name')->nullable();
            $table->string('slug')->unique()->index();
			$table->timestamp('start_at');
			$table->timestamp('end_at')->nullable();
            $table->mediumText('description')->nullable();
            $table->smallInteger('max_guest')->unsigned()->default(0);
            $table->boolean('is_published')->nullable()->default(false);
			$table->json('options')->nullable();
            $table->integer('relation_id')->unsigned()->default(0);
            $table->morphs('eventable');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('sas_erp_events_guests', function ($table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->primary(['user_id', 'event_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('sas_erp_events_guests');
        //Schema::dropIfExists('sas_erp_events_occurrences');
        Schema::dropIfExists('sas_erp_events');
    }
}
