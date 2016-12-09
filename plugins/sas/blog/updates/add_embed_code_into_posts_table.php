<?php namespace Sas\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddEmbedCodeIntoPostsTable extends Migration {

    public function up()
    {
        Schema::table('sas_blog_posts', function($table) {
            $table->string('sas_embed_code')->nullable();
        });
    }

    public function down()
    {
        $table->dropDown(['sas_embed_code']);
    }
}