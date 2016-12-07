<?php namespace Sas\Forum\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class DropWatchesTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('sas_forum_topic_watches');
        Schema::dropIfExists('sas_forum_channel_watches');
    }

    public function down()
    {
        // ...
    }
}
