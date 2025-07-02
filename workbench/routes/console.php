<?php

declare(strict_types=1);

use Givebutter\Laravel\Facades\Givebutter;
use Illuminate\Support\Facades\Artisan;

Artisan::command('givebutter:campaigns', function () {
    $campaigns = Givebutter::campaigns()->list();
    dd($campaigns);
})->purpose('Retrieves campaign information from Givebutter.');
