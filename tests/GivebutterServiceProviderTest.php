<?php

declare(strict_types=1);

use Givebutter\Client;
use Givebutter\Contracts\ClientContract;
use Givebutter\Exceptions\GivebutterClientException;
use Givebutter\Laravel\GivebutterServiceProvider;
use Illuminate\Config\Repository;

covers(GivebutterServiceProvider::class);

describe(GivebutterServiceProvider::class, function () {
    beforeEach(function () {
        $this->app = app();
    });

    it('binds the client on the container', function () {
        // Arrange & Act
        $this->app->bind('config', fn () => new Repository([
            'givebutter' => [
                'api_key' => 'test',
            ],
        ]));

        new GivebutterServiceProvider($this->app)->register();

        // Assert
        expect($this->app->get(Client::class))->toBeInstanceOf(Client::class);
    });

    it('binds the client on the container as singleton', function () {
        // Arrange & Act
        $this->app->bind('config', fn () => new Repository([
            'givebutter' => [
                'api_key' => 'test',
            ],
        ]));

        new GivebutterServiceProvider($this->app)->register();
        $client = $this->app->get(Client::class);

        // Assert
        expect($this->app->get(Client::class))->toBe($client);
    });

    it('requires an api key', function () {
        // Act
        $this->app->bind('config', fn () => new Repository([]));

        // Act
        new GivebutterServiceProvider($this->app)->register();

        // Assert
        $this->app->get(Client::class);
    })->throws(
        GivebutterClientException::class,
        'API key is required to call Givebutter\'s API.',
    );

    it('provides using class, contract, and string references', function () {
        // Arrange & Act
        $provides = new GivebutterServiceProvider($this->app)->provides();

        // Assert
        expect($provides)->toBe([
            Client::class,
            ClientContract::class,
            'givebutter',
        ]);
    });
});
