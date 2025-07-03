<?php

declare(strict_types=1);

namespace Givebutter\Laravel;

use Carbon\Laravel\ServiceProvider;
use Givebutter\Client;
use Givebutter\Contracts\ClientContract;
use Givebutter\Exceptions\GivebutterClientException;
use Givebutter\Givebutter;
use Givebutter\Laravel\Commands\InstallCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Override;

final class GivebutterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(ClientContract::class, static function (): Client {
            /** @var ?string $apiKey */
            $apiKey = config('givebutter.api_key');

            /** @var ?string $apiKey */
            $baseUri = config('givebutter.base_uri', Client::API_BASE_URL);

            /** @var ?int $timeout */
            $timeout = config('givebutter.timeout', 30);

            if ($apiKey === null) {
                throw GivebutterClientException::apiKeyMissing();
            }

            $client = Givebutter::builder()
                ->withApiKey($apiKey)
                ->withHttpClient(new \GuzzleHttp\Client(
                    [
                        'timeout' => $timeout,
                    ]
                ));

            if (! is_string($baseUri)) {
                $baseUri = Client::API_BASE_URL;
            }

            if ($baseUri !== '') {
                $client = $client->withBaseUri($baseUri);
            }

            return $client->build();
        });

        $this->app->alias(ClientContract::class, 'givebutter');
        $this->app->alias(ClientContract::class, Client::class);
    }

    #[Override]
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/givebutter.php' => config_path('givebutter.php'),
            ]);

            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    #[Override]
    public function provides(): array
    {
        return [
            Client::class,
            ClientContract::class,
            'givebutter',
        ];
    }
}
