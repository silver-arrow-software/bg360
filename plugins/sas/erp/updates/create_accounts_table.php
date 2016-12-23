<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class SasCreateAccountsTable extends Migration
{
    public function up() {
        Schema::create('sas_erp_accounts', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description');
			$table->integer('accountable_id')->unsigned();
			$table->string('accountable_type')->nullable();
			$table->bigInteger('balance');
			$table->string('currency', 10)->default('đồng');
            $table->timestamps();
        });

        Schema::create('sas_erp_account_transactions', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->bigInteger('amount');
            $table->integer('user_id')->unsigned();
            $table->text('description');
            $table->timestamps();
        });

        /*Schema::create('sas_erp_incomes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('description');
            $table->integer('account_id')->unsigned();
			$table->bigInteger('amount');
            $table->timestamps();
        });

        Schema::create('sas_erp_expenses', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('description');
            $table->integer('account_id')->unsigned();
			$table->bigInteger('amount');
            $table->timestamps();
        });*/
	}

    public function down() {
        Schema::dropIfExists('sas_erp_accounts');
        Schema::dropIfExists('sas_erp_account_transactions');
        // Schema::dropIfExists('sas_erp_incomes');
        // Schema::dropIfExists('sas_erp_expenses');
    }
}
