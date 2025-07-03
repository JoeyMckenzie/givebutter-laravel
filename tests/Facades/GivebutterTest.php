<?php

declare(strict_types=1);

use Givebutter\Laravel\Facades\Givebutter;
use Givebutter\Laravel\GivebutterServiceProvider;
use Givebutter\Resources\CampaignsResource;
use Givebutter\Responses\Campaigns\GetCampaignResponse;
use Givebutter\Testing\Fixtures\Campaigns\GetCampaignFixture;
use Illuminate\Config\Repository;
use PHPUnit\Framework\ExpectationFailedException;

covers(Givebutter::class);

describe(Givebutter::class, function (): void {
    beforeEach(function (): void {
        $this->app = app();
        $this->app->bind('config', fn (): Repository => new Repository([
            'givebutter' => [
                'api_key' => 'test',
            ],
        ]));
    });

    it('can resolve resources', function (): void {
        // Arrange
        new GivebutterServiceProvider($this->app)->register();

        // Act
        Givebutter::setFacadeApplication($this->app);
        $campaigns = Givebutter::campaigns();

        // Assert
        expect($campaigns)->toBeInstanceOf(CampaignsResource::class);
    });

    it('can return fake responses', function (): void {
        // Arrange
        Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class, [
                'description' => 'test description',
            ]),
        ]);

        // Act
        $campaign = Givebutter::campaigns()->get(42);

        // Assert
        expect($campaign['description'])->toBe('test description');
    });

    it('can throw exceptions if there are no more provided responses', function (): void {
        // Arrange
        Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Act & Assert
        Givebutter::campaigns()->get(42);
        Givebutter::campaigns()->get(123);
    })->throws(Exception::class, 'No fake responses left.');

    it('can append more fake responses', function (): void {
        // Arrange
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class, [
                'description' => 'test description',
            ]),
        ]);

        $fake->proxy->addResponses([
            GetCampaignResponse::fake(GetCampaignFixture::class, [
                'description' => 'another test description',
            ]),
        ]);

        // Act
        $campaign = Givebutter::campaigns()->get(42);

        // Assert
        expect($campaign)
            ->description->toBe('test description');

        $campaign = Givebutter::campaigns()->get(123);

        expect($campaign)
            ->description->toBe('another test description');
    });

    it('can assert a request was sent', function (): void {
        // Arrange
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Act
        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        // Assert
        $fake->proxy->assertSent(CampaignsResource::class, fn (string $method, array $parameters): bool => $method === 'create' &&
            $parameters[0]['title'] === 'test title' &&
            $parameters[0]['description'] === 'test description');
    });

    it('can throw an exception if a request was not sent', function (): void {
        // Arrange
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Act & Assert
        $fake->proxy->assertSent(CampaignsResource::class, fn (string $method, array $parameters): bool => $method === 'create' &&
            $parameters[0]['title'] === 'test title' &&
            $parameters[0]['description'] === 'test description');
    })->throws(ExpectationFailedException::class);

    it('can assert a request was sent any number of times', function (): void {
        // Arrange
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Act
        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        $fake->campaigns()->create([
            'title' => 'another test title',
            'description' => 'another test description',
        ]);

        // Assert
        $fake->proxy->assertSent(CampaignsResource::class, 2);
    });

    it('can throw an exception if a request was not sent any number of times', function (): void {
        // Arrange
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Act
        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        // Assert
        $fake->proxy->assertSent(CampaignsResource::class, 2);
    })->throws(ExpectationFailedException::class);

    it('can assert a request was not sent', function (): void {
        // Arrange & Act
        $fake = Givebutter::fake();

        // Assert
        $fake->proxy->assertNotSent(CampaignsResource::class);
    });

    it('throws an exception if an unexpected request was sent', function (): void {
        // Arrange
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Act
        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        // Assert
        $fake->proxy->assertNotSent(CampaignsResource::class);
    })->throws(ExpectationFailedException::class);

    it('can assert a request was not sent on the resource', function (): void {
        // Arrange & Act
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Assert
        $fake->proxy->assertNotSent(CampaignsResource::class);
    });

    it('can assert no request was sent', function (): void {
        // Arrange & Act
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Assert
        $fake->proxy->assertNothingSent();
    });

    it('throws an exception if any request was sent when non was expected', function (): void {
        // Arrange
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        // Act
        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        // Assert
        $fake->proxy->assertNothingSent();
    })->throws(ExpectationFailedException::class);
});
