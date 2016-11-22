<?php namespace Sas\Erp\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class SasAddProfileToUsers extends Migration {
    public function up() {
        if(!Schema::hasColumn('users', 'profile_id')) {
            Schema::table('users', function($table) {
                $table->integer('profile_id')->unsigned()->index()->nullable();
            });
        }
        /*if(!Schema::hasColumn('users', 'address_id')) {
            Schema::table('users', function($table) {
                $table->integer('address_id')->unsigned()->nullable();
            });
        }*/
        if(!Schema::hasColumn('users', 'street_addr')) {
            Schema::table('users', function($table) {
                $table->string('street_addr', 200)->nullable();
            });
        }
        if(!Schema::hasColumn('users', 'district')) {
            Schema::table('users', function($table) {
                $table->string('district', 50)->nullable();
            });
        }
        if(!Schema::hasColumn('users', 'state_id')) {
            Schema::table('users', function($table) {
                $table->integer('state_id')->unsigned()->nullable()->index();
            });
        }
        if(!Schema::hasColumn('users', 'country_id')) {
            Schema::table('users', function($table) {
                $table->integer('country_id')->unsigned()->nullable()->index();
            });
        }
        if(!Schema::hasColumn('users', 'mobile')) {
            Schema::table('users', function($table) {
                $table->string('mobile', 11)->nullable();
            });
        }
        if(!Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function($table) {
                $table->integer('company_id')->unsigned()->nullable();
            });
        }
        if(!Schema::hasColumn('users', 'code')) {
            Schema::table('users', function($table) {
                $table->string('code', 20)->nullable();
            });
        }
        if(!Schema::hasColumn('users', 'point')) {
            Schema::table('users', function($table) {
                $table->integer('point')->unsigned()->default(0);
            });
        }
    }

    public function down() {
        if(Schema::hasColumn('users', 'profile_id')) {
            Schema::table('users', function($table) {
                $table->dropColumn('profile_id');
            });
        }
        /*if(Schema::hasColumn('users', 'address_id')) {
            Schema::table('users', function($table) {
                $table->dropColumn('address_id');
            });
        }*/
        if(Schema::hasColumn('users', 'street_addr')) {
            Schema::table('users', function($table) {
                $table->dropColumn('street_addr');
            });
        }
        if(Schema::hasColumn('users', 'district')) {
            Schema::table('users', function($table) {
                $table->dropColumn('district');
            });
        }
        if(Schema::hasColumn('users', 'state_id')) {
            Schema::table('users', function($table) {
                $table->dropColumn('state_id');
            });
        }
        if(Schema::hasColumn('users', 'country_id')) {
            Schema::table('users', function($table) {
                $table->dropColumn('country_id');
            });
        }
        if(Schema::hasColumn('users', 'mobile')) {
            Schema::table('users', function($table) {
                $table->dropColumn('mobile');
            });
        }
        if(Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function($table) {
                $table->dropColumn('company_id');
            });
        }
        if(Schema::hasColumn('users', 'code')) {
            Schema::table('users', function($table) {
                $table->dropColumn('code');
            });
        }
        if(Schema::hasColumn('users', 'point')) {
            Schema::table('users', function($table) {
                $table->dropColumn('point');
            });
        }
    }
}