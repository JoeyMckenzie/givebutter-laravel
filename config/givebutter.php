<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Givebutter API Key
    |--------------------------------------------------------------------------
    |
    | Your Givebutter API key used to authenticate requests to the API. You may
    | generate and manage keys from your Givebutter dashboard, and place
    | within your environment file for the package to read from.
    |
    */

    'api_key' => env('GIVEBUTTER_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Base URI
    |--------------------------------------------------------------------------
    |
    | The base URI for the Givebutter API. You may change this if you are using
    | a prior version endpoint or proxy. Defaults to the most recent available
    | Givebutter API URL based on the current document version.
    |
    */

    'base_uri' => env('GIVEBUTTER_BASE_URI'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait for a response before timing out. You may
    | configure this as an environment variable to be used on all outbound
    | requests to Givebutter's API, which defaults to 30 seconds.
    |
    */

    'request_timeout' => env('GIVEBUTTER_REQUEST_TIMEOUT', 30),
];
