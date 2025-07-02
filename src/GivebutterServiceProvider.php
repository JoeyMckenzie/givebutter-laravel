<?php

declare(strict_types=1);

namespace Givebutter\Laravel;

use Carbon\Laravel\ServiceProvider;
use Givebutter\Client;
use Givebutter\Contracts\ClientContract;
use Givebutter\Givebutter;
use Givebutter\Laravel\Commands\InstallCommand;
use Illuminate\Contracts\Support\DeferrableProvider;

final class GivebutterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(ClientContract::class, static function (): Client {
            $apiKey = config('givebutter.api_key');
            $baseUri = config('givebutter.base_uri', Client::API_BASE_URL);
            $timeout = config('givebutter.timeout', 30);
            $client = Givebutter::builder()
                ->withApiKey($apiKey)
                ->withHttpClient(new \GuzzleHttp\Client(
                    [
                        'timeout' => $timeout,
                    ]
                ));

            if ($baseUri === null) {
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

    /**
     * Bootstrap any application services.
     */
    #[\Override]
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
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    #[\Override]
    public function provides(): array
    {
        return [
            Client::class,
            ClientContract::class,
            'givebutter',
        ];
    }
}
