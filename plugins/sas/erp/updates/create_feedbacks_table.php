<?php namespace Sas\Erp\Updates;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateFeedbacksTable extends Migration {
    public function up() {
        Schema::create('sas_erp_feedback_channels', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->string('code')
                ->unique()
                ->index();
            $table->boolean('prevent_save_database');

            $table->string('method');
            $table->longText('method_data')->nullable();
        });
        Schema::create('sas_erp_feedbacks', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->boolean('archived')->default(false);
            $table->integer('channel_id')->unsigned();

            $table->foreign('channel_id')
                ->references('id')
                ->on('sas_erp_feedback_channels')
                ->onDelete('cascade');
        });
	}

    public function down() {
        Schema::dropIfExists('sas_erp_feedbacks');
        Schema::dropIfExists('sas_erp_feedback_channels');
    }
}
