<div align="center" style="padding-top: 2rem;">
    <img src="art/logo.png" height="300" width="300" alt="logo"/>
    <div style="display: inline-block; margin-top: 2rem">
        <img src="https://img.shields.io/packagist/v/joeymckenzie/givebutter-laravel.svg" alt="packgist downloads" />
        <img src="https://img.shields.io/github/actions/workflow/status/joeymckenzie/givebutter-laravel/run-ci.yml?branch=main&label=ci" alt="ci" />
        <img src="https://img.shields.io/github/actions/workflow/status/joeymckenzie/givebutter-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style" alt="packgist downloads" />
        <img src="https://img.shields.io/packagist/dt/joeymckenzie/givebutter-laravel.svg" alt="packgist downloads" />
        <img src="https://codecov.io/gh/JoeyMckenzie/givebutter-laravel/graph/badge.svg?token=9LZK1YDGKG" alt="codecov coverage report"/> 
    </div>
</div>

# 🧈 Givebutter for Laravel

**Givebutter PHP** for Laravel is a PHP API client that allows you to interact with the
[Givebutter API](https://docs.givebutter.com). This package provides integration with Laravel for a seamless experience.

> **Note:** This repository contains the integration code of the **Givebutter PHP** for Laravel. If you want to use
> the **Givebutter PHP** client in a framework-agnostic way, take a look at
> the [joeymckenzie/givebutter-php](https://github.com/joeymckenzie/givebutter-php) repository.

## Table of Contents

- [Getting started](#getting-started)
- [Usage](#usage)
- [Configuration](#usage)
- [Testing](#testing)

## Getting Started

> **Requires [PHP 8.4+](https://www.php.net/releases/)**

Install Givebutter PHP via [Composer](https://getcomposer.org/):

```bash
composer require joeymckenzie/givebutter-laravel
```

Then, run the install command:

```bash
php artisan givebutter:install
```

This will create a `config/givebutter.php` configuration file in your project, which you can modify to your needs
using environment variables. A blank environment variable for the Givebutter API key will be appended to your `.env` and
`.env.example` files.

```env
GIVEBUTTER_API_KEY=...
```

Once the install command finishes, update your `GIVEBUTTER_API_KEY` with an appropriate value. You may use the
`Givebutter` facade to access the Givebutter API:

```php
use Givebutter\Laravel\Facades\Givebutter;

$response = Givebutter::campaigns()->create([
    'title' => 'Campaign title',
    'description' => 'Campaign description.',
    'end_at' => CarbonImmutable::now()->toIso8601String(),
    'goal' => 10000,
    'subtitle' => 'Campaign subtitle',
    'slug' => 'campaignSlug123',
    'type' => 'collect',
]);

echo $response->data(); // GetCampaignResponse::class
echo $response->id; // 42
echo $response->title; // 'Campaign title'
echo $response->goal; // 10000
echo $response->toArray(); // ['id' => 42, ...]
```

## Configuration

Configuration is done via environment variables or directly in the configuration file (`config/givebutter.php`).

### Givebutter API Key

Specify your Givebutter API Key and organization. This will be used to authenticate with the Givebutter API. You can
generate an API key within the Givebutter dashboard under the **Integrations** section.

```env
GIVEBUTTER_API_KEY=
```

### Givebutter API Base URI

The base URI for the Givebutter API. By default, this is set to `https://api.givebutter.com/v1`.

```env
GIVEBUTTER_BASE_URL=
```

### Request Timeout

The timeout may be used to specify the maximum number of seconds to wait for a response. By default, the client will
time out after 30 seconds.

```env
GIVEBUTTER_REQUEST_TIMEOUT=
```

## Usage

For usage examples, take a look at the [joeymckenzie/givebutter-php](https://github.com/joeymckenzie/givebutter-php)
repository.

## Testing

The `Givebutter` facade comes with a `fake()` method that allows you to fake the API responses. Fake responses are
returned in the order they are provided to the `fake()` method. All responses have a `fake()` method that allows you to
easily create a response object by only providing the parameters relevant for your test case.

```php
use Givebutter\Laravel\Facades\Givebutter;
use Givebutter\Responses\Campaigns\GetCampaignResponse;
use Givebutter\Testing\Fixtures\Campaigns;

Givebutter::fake([
    GetCampaignResponse::fake(GetCampaignFixture::class, [
        'description' => 'This is an override of the default fixture data.',
    ]),
]);

$campaign = Givebutter::campaigns()->create([
    'title' => 'Campaign title',
    'description' => 'Campaign description.',
    'end_at' => CarbonImmutable::now()->toIso8601String(),
    'goal' => 10000,
    'subtitle' => 'Campaign subtitle',
    'slug' => 'campaignSlug123',
    'type' => 'collect',
]);

expect($campaign->description)->toBe('This is an override of the default fixture data.');
```

Fake responses expect a data fixture as well. Data fixtures are available for each response type. See the
[joeymckenzie/givebutter-php](https://github.com/JoeyMckenzie/givebutter-php/tree/main/src/Testing/Fixtures) repository
for the available fixture responses.

After the request has been sent, there are various methods to ensure that the expected requests were sent:

```php
// assert completion create request was sent
Givebutter::assertSent(Campaigns::class, function (string $method, array $parameters): bool {
    return $method === 'create' &&
        $parameters[0]['title'] === 'Campaign title' &&
        $parameters[0]['description'] === 'This is an override of the default fixture data.';
});
```

For more testing examples, take a look at the
[joeymckenzie/givebutter-php](https://github.com/joeymckenzie/givebutter-php#testing) repository.

---

Givebutter PHP for Laravel is an open-sourced software licensed under the
**[MIT license](https://opensource.org/licenses/MIT)**.