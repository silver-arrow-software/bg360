<?php namespace Sas\Erp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;
use System\Classes\PluginManager;
use Illuminate\Support\Facades\DB;

class SasCreateTagsTable extends Migration {
    private $dbType;

    public function up() {
        $this->dbType = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        Schema::create('sas_erp_tags', function($table) {
            $this->dbSpecificSetup($table);

            $table->increments('id');
            $table->string('name', 30)->unique()->nullable();
            $table->integer('created_by')->unsigned();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('sas_erp_taggables', function($table) {
            $this->dbSpecificSetup($table);

            $table->integer('tag_id')->unsigned();
            $table->integer('taggable_id')->unsigned();
            $table->string('taggable_type');
            $table->index(['tag_id', 'taggable_id', 'taggable_type']);
        });
        
        /*Schema::create('sas_erp_tag_place', function($table) {
            $this->dbSpecificSetup($table);

            $table->integer('tag_id')->unsigned();
            $table->integer('place_id')->unsigned();
            $table->index(['tag_id', 'place_id']);
        });

        Schema::create('sas_erp_tag_product', function($table) {
            $this->dbSpecificSetup($table);

            $table->integer('tag_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->index(['tag_id', 'product_id']);
        });

        Schema::create('sas_erp_tag_post', function($table) {
            $this->dbSpecificSetup($table);

            $table->integer('tag_id')->unsigned();
            $table->integer('post_id')->unsigned();
            $table->index(['tag_id', 'post_id']);
        });*/
    }

    public function down() {
        /*Schema::dropIfExists('sas_erp_tag_place');
        Schema::dropIfExists('sas_erp_tag_product');
        Schema::dropIfExists('sas_erp_tag_post');*/
        Schema::dropIfExists('sas_erp_tags');
    }


    /**
     * @param $table
     */
    private function dbSpecificSetup(&$table) {
        switch ($this->dbType) {
            case 'pgsql':
                break;
            case 'mysql':
                //@todo SET sql_mode='ANSI_QUOTES';
                $table->engine = 'InnoDB';
                break;
        }
    }
}