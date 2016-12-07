<?php

return [
    'plugin' => [
        'name' => 'Sas Ext. Blog',
        'description' => 'Extending RainLab Blog for embed and export pdf',
        'sas_embed_code' => 'Sas Embed Code'
    ],
    'member' => [
        'page_name' => 'Member Page',
        'page_help' => 'Page name to use for clicking on a Member.'
    ],
    'embedposts' => [
        'posts_name' => 'Embed Posts',
        'posts_self_desc' => 'Attach posts list to any page.',
        'posts_title' => 'Parent Posts',
        'posts_desc' => 'Specify the posts list to create the new category in',
        'embed_title' => 'Embed code param',
        'embed_desc' => 'A unique code for the generated category. A routing parameter can also be used.',
        'post_name' => 'Post code param',
        'post_desc' => 'The URL route parameter used for looking up a post by its slug.'
    ],
    'slugpost' => [
        'post_name' => 'Embed Post',
        'post_self_desc' => 'Attach a post to any page.',
        'target_name' => 'Target Post',
        'target_desc' => 'Specify the post to create the new post or list',
        'slug_title' => 'Slug Code',
        'slug_desc' => 'A unique code for the show post. A routing parameter can also be used.'
    ]
];
