<?php

declare(strict_types=1);

namespace Givebutter\Laravel\Facades;

use Givebutter\Client;
use Givebutter\Resources\CampaignsResource;
use Givebutter\Resources\ContactsResource;
use Givebutter\Resources\FundsResource;
use Givebutter\Resources\PayoutsResource;
use Givebutter\Resources\PlansResource;
use Givebutter\Resources\TicketsResource;
use Givebutter\Resources\TransactionsResource;
use Givebutter\Testing\ClientFake;
use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;
use Wrapkit\Contracts\ResponseContract;

/**
 * @method static CampaignsResource campaigns()
 * @method static ContactsResource contacts()
 * @method static FundsResource funds()
 * @method static PayoutsResource payouts()
 * @method static PlansResource plans()
 * @method static TicketsResource tickets()
 * @method static TransactionsResource transactions()
 *
 * @see Client
 */
final class Givebutter extends Facade
{
    /**
     * @param  array<array-key, ResponseContract|ResponseInterface|string>  $responses
     */
    public static function fake(array $responses = []): ClientFake // @phpstan-ignore-line missingType.generics
    {
        $fake = new ClientFake($responses); // @phpstan-ignore-line argument.type
        self::swap($fake);

        return $fake;
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'givebutter';
    }
}
