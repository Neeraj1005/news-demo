<?php

return [
    'feeds' => [
        'main' => [

            'items' => '',

            'url' => '/rss_latest_feed',

            'logo_url' => '',

            'title' => 'Latest Articles - '.env('APP_NAME'),

            'description' => 'All Latest Articles.',

            'language' => 'en-US',

            'copyright' => 'Copyright:(C) '.env('APP_NAME').' '.date('Y'). ' All Rights Reserved',

            'type' => 'application/atom+xml',
        ],
    ],
];


?>
