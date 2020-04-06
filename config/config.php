<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DNS - prefetch tags
    |--------------------------------------------------------------------------
    |
    | These are the tag types to use for when looking for host-names. The hosts
    | found will automatically be added as dns-prefetch tags.
    |
    | eg. ['img', 'script']
    |     ['*'] for any tag type
    |     [] or NULL to turn automatic detection off
    |
    */

    'dns_prefetch_tags' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CSS Timings
    |--------------------------------------------------------------------------
    |
    | When turned on, the amount of time taken to process the css will be added
    | at the end.
    |
    */

    'css_timings' => false,

];
