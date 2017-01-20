<?php namespace Sas\Blog\Updates;

use October\Rain\Database\Updates\Migration;
use DbDongle;

class UpdateTimestampsNullable extends Migration
{
    public function up()
    {
        DbDongle::disableStrictMode();

        DbDongle::convertTimestamps('sas_blog_posts');
        DbDongle::convertTimestamps('sas_blog_categories');
    }

    public function down()
    {
        // ...
    }
}
