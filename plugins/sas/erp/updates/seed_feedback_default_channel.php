<?php namespace Sas\Erp\Updates;

use Sas\Erp\Models\FeedbackChannel;

class SeedFeedbackDefaultChannel extends \Seeder
{
    public function run() {
        FeedbackChannel::create([
            'name' => 'Default',
            'code' => 'default',
            'method' => 'email',
			'method_data' => [
                "email_destination" => "",
                "subject" => "A feedback from boardgame360 website",
                "template" => "<h1>You have just received a feedback from your site <a href=\"{{ host }}\">{{ serverName }}<\/a><\/h1>\r\n<p>Here is the contact information: {{ name }} &lt;<a href=\\\"mailto:{{ email }}\">{{ email }}<\/a>&gt;<\/p>\r\n<p><\/p>\r\n<h2>The message:<\/h2>\r\n<p>{{ message }}<\/p>",
                "channels" => "0"
            ],
            'prevent_save_database' => false
        ]);
    }
}
