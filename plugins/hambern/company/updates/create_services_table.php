<?php namespace Hambern\Company\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class CreateServicesTable extends Migration
{

	public function up()
	{
		Schema::create('hambern_company_services', function ($table) {
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('name');
			$table->string('icon');
			$table->text('description');
			$table->date('published_at')->nullable();
			$table->nullableTimestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('hambern_company_services');
	}

}
