<?php namespace Sas\Forum\Updates;

use October\Rain\Database\Updates\Migration;
use DbDongle;

class UpdateTimestampsNullable extends Migration
{
    public function up()
    {
        DbDongle::disableStrictMode();

        DbDongle::convertTimestamps('sas_forum_channels');
        DbDongle::convertTimestamps('sas_forum_members');
        DbDongle::convertTimestamps('sas_forum_posts');
        DbDongle::convertTimestamps('sas_forum_topic_followers');
        DbDongle::convertTimestamps('sas_forum_topics');
    }

    public function down()
    {
        // ...
    }
}
