<?php namespace Sas\Forum\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddEmbedCode extends Migration
{
    public function up()
    {
        Schema::table('sas_forum_channels', function($table)
        {
            $table->string('embed_code')->nullable()->index();
        });

        Schema::table('sas_forum_topics', function($table)
        {
            $table->string('embed_code')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('sas_forum_channels', function($table)
        {
            $table->dropColumn('embed_code');
        });

        Schema::table('sas_forum_topics', function($table)
        {
            $table->dropColumn('embed_code');
        });
    }
}
