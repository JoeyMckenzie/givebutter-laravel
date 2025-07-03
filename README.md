<div align="center" style="padding-top: 2rem;">
    <img src="art/logo.png" height="300" width="300" alt="logo"/>
    <div style="display: inline-block; margin-top: 2rem">
        <img src="https://img.shields.io/packagist/v/joeymckenzie/givebutter-php.svg" alt="packgist downloads" />
        <img src="https://img.shields.io/github/actions/workflow/status/joeymckenzie/givebutter-php/run-ci.yml?branch=main&label=ci" alt="ci" />
        <img src="https://img.shields.io/github/actions/workflow/status/joeymckenzie/givebutter-php/fix-php-code-style-issues.yml?branch=main&label=code%20style" alt="packgist downloads" />
        <img src="https://img.shields.io/packagist/dt/joeymckenzie/givebutter-php.svg" alt="packgist downloads" />
        <img src="https://codecov.io/gh/JoeyMckenzie/givebutter-php/graph/badge.svg?token=9LZK1YDGKG" alt="codecov coverage report"/> 
    </div>
</div>

# 🧈 Givebutter PHP

Givebutter PHP is a plug 'n play HTTP client for Givebutter's public API.

## Table of Contents

- [Getting started](#getting-started)
- [Notes](#notes)
- [Usage](#usage)
    - [Campaigns](#campaigns)
    - [Campaign Members](#campaign-members)
    - [Campaign Teams](#campaign-teams)
    - [Contacts](#contacts)
    - [Tickets](#tickets)
    - [Transactions](#transactions)
    - [Payouts](#payouts)
    - [Funds](#funds)

## Getting started

The client is available as a composer packager that can be installed in any project using composer:

```bash
composer require joeymckenzie/givebutter-php
```

Since the client is compatible with any PSR-18 HTTP client, any commonly used HTTP client can be used thanks
to our dependency on `php-http/discovery`. Once both dependencies have been installed, you may start interacting
with [Givebutter's API](https://docs.givebutter.com/reference/reference-getting-started/):

```php
/** @var string $apiKey */
$apiKey = $_ENV['GIVEBUTTER_API_KEY'];
$client = Givebutter::client($apiKey);

// Create a campaign
$createdCampaign = $client
    ->campaigns()
    ->create([
        'description' => 'This is a test campaign.',
        'end_at' => CarbonImmutable::now()->toIso8601String(),
        'goal' => 1000,
        'subtitle' => 'subtitle',
        'slug' => md5(uniqid('', true)),
        'title' => 'title',
        'type' => 'collect',
    ]);

// Get a campaign
$campaign = $client
    ->campaigns()
    ->get(441507);

// Get all campaigns
$campaigns = $client
    ->campaigns()
    ->list();

// Update a campaign
$updatedCampaign = $client
    ->campaigns()
    ->update($campaign->id, [
        'description' => 'This is a test campaign.',
        'goal' => 1500,
    ]);

// Delete a campaign
$deleteResponse = $client
    ->campaigns()
    ->delete($campaign->id);
```

For a comprehensive set of examples, take a look at the [examples](/examples) directory.

## Notes

There's some current discrepancies between the API documentation and what the API actually returns, which several
resources drifting from their schema definition. I do the best I can without internal knowledge of the API to adhere
to the response schema, though it may be possible there will be breaking changes.

Due to the API response structure and the lack of resource enveloping, the response fields themselves may be `null`
at any point. One should always check for errors before attempting to use any properties on the responses:

```php
$createdCampaign = $client
    ->campaigns()
    ->create([
        'description' => 'This is a test campaign.',
        'end_at' => CarbonImmutable::now()->toIso8601String(),
        'goal' => 1000,
        'subtitle' => 'subtitle',
        'slug' => md5(uniqid('', true)),
        'title' => 'title',
        'type' => 'collect',
    ]);

if ($createdCampaign->hasErrors()) {
    // Do some error handling...
} else {
    // Safely dereference response properties
    assert($campaign->id !== null);

    // Update a campaign
    $updatedCampaign = $client
        ->campaigns()
        ->update($createdCampaign->id, [
            'description' => 'This is another test campaign.',
            'goal' => 1500,
        ]);
}
```

## Usage

### Campaigns

#### Create a campaign

Creates a campaign from a specified payload.

```php
$response = $client
    ->campaigns()
    ->create([
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

#### Get all campaigns

Gets a list of available campaigns. Optionally, accepts a scope parameter.

```php
$response = $client
    ->campaigns()
    ->list();

echo $response->data(); // array<int, GetCampaignResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 42, ...], 'meta' => [...], 'links' => [...]]
```

#### Get a campaign

Gets a single campaign.

```php
$response = $client
    ->campaigns()
    ->get(42);

echo $response->data(); // GetCampaignResponse::class
echo $response->id; // 42
echo $response->title; // 'Campaign title'
echo $response->goal; // 10000
echo $response->toArray(); // ['id' => 42, ...]
```

#### Update a campaign

Updates a campaign from a specified payload.

```php
$response = $client
    ->campaigns()
    ->update(42, [
        'description' => 'Updated campaign description.',
        'goal' => 20000,
    ]);

echo $response->data(); // GetCampaignResponse::class
echo $response->id; // 42
echo $response->title; // 'Campaign title'
echo $response->goal; // 20000
echo $response->toArray(); // ['id' => 42, ...]
```

#### Delete a campaign

Deletes a campaign.

```php
$response = $client
    ->campaigns()
    ->delete(42);

echo $response->getStatusCode(); // 200
```

### Campaign Members

#### Get all campaign members

Gets a list of available campaign members.

```php
$response = $client
    ->campaigns()
    ->members()
    ->list(123);

echo $response->data(); // array<int, GetCampaignMembersResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 123, ...], 'meta' => [...], 'links' => [...]]
```

#### Get a campaign member

Gets a single campaign member.

```php
$response = $client
    ->campaigns()
    ->members()
    ->get(123, 42);

echo $response->data(); // GetCampaignMemberResponse::class
echo $response->id; // 123
echo $response->firstName; // 'John'
echo $response->lastName; // 'Smith'
echo $response->raised; // 10000
echo $response->toArray(); // ['id' => 123, ...]
```

#### Delete a campaign member

Deletes a campaign member.

```php
$response = $client
    ->campaigns()
    ->members()
    ->delete(123, 42);

echo $response->getStatusCode(); // 200
```

### Campaign Teams

#### Get all campaign teams

Gets a list of available campaign teams.

```php
$response = $client
    ->campaigns()
    ->teams()
    ->list(123);

echo $response->data(); // array<int, GetCampaignTeamsResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 123, ...], 'meta' => [...], 'links' => [...]]
```

#### Get a campaign team

Gets a single campaign team.

```php
$response = $client
    ->campaigns()
    ->teams()
    ->get(123, 42);

echo $response->data(); // GetCampaignTeamResponse::class
echo $response->id; // 123
echo $response->name; // 'Team 1'
echo $response->logo; // 'https://domain.com/photo123'
echo $response->raised; // 10000
echo $response->toArray(); // ['id' => 123, ...]
```

### Contacts

#### Create a contact

Creates a contact from a specified payload.

```php
$response = $client
    ->contacts()
    ->create([
        'first_name' => 'Michael',
        'middle_name' => 'Gary',
        'last_name' => 'Scott',
        'email' => [
            [
                'type' => 'work',
                'value' => 'michael.scott@dundermifflin.com',
            ],
        ],
        'phones' => [
            [
                'type' => 'work',
                'value' => '+15303567734',
            ],
        ],
        'addresses' => [
            [
                'address_1' => '123 Paper St.',
                'city' => 'Scranton',
                'state' => 'PA',
                'zipcode' => '18507',
                'country' => 'US',
            ],
        ],
        'tags' => [
            'paper',
            'dunder mifflin',
        ],
        'dob' => '03/15/1965',
        'company' => 'Dunder Mifflin',
        'title' => 'Regional Manager',
        'twitter_url' => 'https://twitter.com/dundermifflin',
        'linkedin_url' => 'https://linkedin.com/in/dundermifflin',
        'facebook_url' => 'https://facebook.com/dundermifflin',
    ]);

echo $response->data(); // GetContactResponse::class
echo $response->id; // 42
echo $response->firstName; // 'Michael'
echo $response->lastName; // 'Scott'
echo $response->toArray(); // ['id' => 42, ...]
```

#### Get all contacts

Gets a list of available contacts. Optionally, accepts a scope parameter.

```php
$response = $client
    ->contacts()
    ->list();

echo $response->data(); // array<int, GetContactResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 42, ...], 'meta' => [...], 'links' => [...]]
```

#### Get a contact

Gets a single contact.

```php
$response = $client
    ->contact()
    ->get(42);

echo $response->data(); // GetContactResponse::class
echo $response->id; // 42
echo $response->firstName; // 'Michael'
echo $response->lastName; // 'Scott'
echo $response->toArray(); // ['id' => 42, ...]
```

#### Update a contact

Updates a contact from a specified payload.

```php
$response = $client
    ->campaigns()
    ->update(42, [
        'first_name' => 'Michael',
        'last_name' => 'Scarn',
        'company' => 'CIA',
        'title' => 'Secret Agent'
    ]);

echo $response->data(); // GetContactResponse::class
echo $response->firstName; // 'Michael'
echo $response->lastName; // 'Scarn'
echo $response->company; // 'CIA'
echo $response->title; // 'Secret Agent'
echo $response->toArray(); // ['id' => 42, ...]
```

#### Archive a contact

Archives a contact.

```php
$response = $client
    ->contacts()
    ->archive(42);

echo $response->getStatusCode(); // 200
```

#### Restore a contact

Gets an archived contact.

```php
$response = $client
    ->contacts()
    ->restore(42);

echo $response->data(); // GetContactResponse::class
echo $response->id; // 42
echo $response->firstName; // 'Michael'
echo $response->lastName; // 'Scott'
echo $response->toArray(); // ['id' => 42, ...]
```

### Tickets

#### Get all tickets

Gets a list of tickets.

```php
$response = $client
    ->tickets()
    ->list();

echo $response->data(); // array<int, GetTicketsResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 'abc123', ...], 'meta' => [...], 'links' => [...]]
```

#### Get a ticket

Gets a single ticket.

```php
$response = $client
    ->tickets()
    ->get('ab123');

echo $response->data(); // GetTicketResponse::class
echo $response->firstName; // 'Michael'
echo $response->lastName; // 'Scott'
echo $response->price; // 100
echo $response->toArray(); // ['id' => 'abc123', ...]
```

### Transactions

#### Create a transaction

Creates a transaction from a specified payload.

```php
$response = $client
    ->transactions()
    ->create([
        'amount' => 100.00,
        'method' => 'cash',
        'transacted_at' => CarbonImmutable::now()->addHour()->format('m/d/y'),
        'campaign_code' => 'DEF456',
        'first_name' => 'Micahel',
        'last_name' => 'Scott',
    ]);

echo $response->data(); // GetTransactionResponse::class
echo $response->amount; // 100.00
echo $response->method; // 'cash'
echo $response->campaignCode; // 'DEF456'
echo $response->toArray(); // ['id' => 'abc123', ...]
```

#### Get all transactions

Gets a list of transactions.

```php
$response = $client
    ->transactions()
    ->list();

echo $response->data(); // array<int, GetTransactionsResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 'abc123', ...], 'meta' => [...], 'links' => [...]]
```

#### Get a transactions

Gets a single transactions.

```php
$response = $client
    ->transactions()
    ->get('ab123');

echo $response->data(); // GetTransactionResponse::class
echo $response->amount; // 100.00
echo $response->method; // 'cash'
echo $response->campaignCode; // 'DEF456'
echo $response->toArray(); // ['id' => 'abc123', ...]
```

### Payouts

#### Get all payouts

Gets a list of payouts.

```php
$response = $client
    ->payouts()
    ->list();

echo $response->data(); // array<int, GetPayoutResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 'abc123', ...], 'meta' => [...], 'links' => [...]]
```

#### Get a payout

Gets a single payout.

```php
$response = $client
    ->payout()
    ->get('ab123');

echo $response->data(); // GetPayoutResponse::class
echo $response->amount; // 100.00
echo $response->fee; // 3.00
echo $response->tip; // 1.00
echo $response->toArray(); // ['id' => 'abc123', ...]
```

### Plans

#### Get all plans

Gets a list of plans.

```php
$response = $client
    ->plans()
    ->list();

echo $response->data(); // array<int, GetPlanResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 'abc123', ...], 'meta' => [...], 'links' => [...]]
```

#### Get a plan

Gets a single plan.

```php
$response = $client
    ->plans()
    ->get('ab123');

echo $response->data(); // GetPlanResponse::class
echo $response->amount; // 100.00
echo $response->firstName; // 'Michael'
echo $response->lastName; // 'Scarn'
echo $response->toArray(); // ['id' => 'abc123', ...]
```

### Funds

#### Create a fund

Creates a fund from a specified payload.

```php
$response = $client
    ->funds()
    ->create("Scott's Tots");

echo $response->data(); // GetFundResponse::class
echo $response->id; // 'abc123'
echo $response->name; // "Scott's Tots"
echo $response->supporters; // 0
echo $response->toArray(); // ['id' => 'abc123', ...]
```

#### Get all funds

Gets a list of all available funds

```php
$response = $client
    ->funds()
    ->list();

echo $response->data(); // array<int, GetFundsResponse::class>
echo $response->meta; // Meta::class
echo $response->links; // Links::class
echo $response->toArray(); // ['data' => ['id' => 'abc123', ...], 'meta' => [...], 'links' => [...]]
```

#### Get a fund

Gets a single fund.

```php
$response = $client
    ->funds()
    ->get('abc123');

echo $response->data(); // GetFundResponse::class
echo $response->id; // 'abc123'
echo $response->name; // "Scott's Tots"
echo $response->supporters; // 0
echo $response->toArray(); // ['id' => 'abc123', ...]
```

#### Update a fund

Updates a fund from a specified payload.

```php
$response = $client
    ->campaigns()
    ->update('abc123', "Scott's Tots", "ST");

echo $response->data(); // GetFundResponse::class
echo $response->id; // 'abc123'
echo $response->name; // "Scott's Tots"
echo $response->code; // 'ST'
echo $response->supporters; // 0
echo $response->toArray(); // ['id' => 'abc123', ...]
```

#### Delete a fund

Deletes a fund.

```php
$response = $client
    ->funds()
    ->delete('abc123');

echo $response->getStatusCode(); // 200
```