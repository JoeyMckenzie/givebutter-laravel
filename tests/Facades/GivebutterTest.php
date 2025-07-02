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

describe(Givebutter::class, function () {
    beforeEach(function () {
        $this->app = app();
        $this->app->bind('config', fn () => new Repository([
            'givebutter' => [
                'api_key' => 'test',
            ],
        ]));
    });

    it('resolves resources', function () {
        // Arrange
        new GivebutterServiceProvider($this->app)->register();

        // Act
        Givebutter::setFacadeApplication($this->app);
        $campaigns = Givebutter::campaigns();

        // Assert
        expect($campaigns)->toBeInstanceOf(CampaignsResource::class);
    });

    it('fake returns the given response', function () {
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

    it('fake throws an exception if there is no more given response', function () {
        Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        Givebutter::campaigns()->get(42);
        Givebutter::campaigns()->get(123);
    })->throws(Exception::class, 'No fake responses left.');

    it('append more fake responses', function () {
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

        $campaign = Givebutter::campaigns()->get(42);

        expect($campaign)
            ->description->toBe('test description');

        $campaign = Givebutter::campaigns()->get(123);

        expect($campaign)
            ->description->toBe('another test description');
    });

    it('fake can assert a request was sent', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        $fake->proxy->assertSent(CampaignsResource::class, fn (string $method, array $parameters): bool => $method === 'create' &&
            $parameters[0]['title'] === 'test title' &&
            $parameters[0]['description'] === 'test description');
    });

    it('fake throws an exception if a request was not sent', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->proxy->assertSent(CampaignsResource::class, fn (string $method, array $parameters): bool => $method === 'create' &&
            $parameters[0]['title'] === 'test title' &&
            $parameters[0]['description'] === 'test description');
    })->throws(ExpectationFailedException::class);

    it('fake can assert a request was sent any number of times', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        $fake->campaigns()->create([
            'title' => 'another test title',
            'description' => 'another test description',
        ]);

        $fake->proxy->assertSent(CampaignsResource::class, 2);
    });

    it('fake throws an exception if a request was not sent any number of times', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        $fake->proxy->assertSent(CampaignsResource::class, 2);
    })->throws(ExpectationFailedException::class);

    it('fake can assert a request was not sent', function () {
        $fake = Givebutter::fake();

        $fake->proxy->assertNotSent(CampaignsResource::class);
    });

    it('fake throws an exception if an unexpected request was sent', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        $fake->proxy->assertNotSent(CampaignsResource::class);
    })->throws(ExpectationFailedException::class);

    it('fake can assert a request was not sent on the resource', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->proxy->assertNotSent(CampaignsResource::class);
    });

    it('fake can assert no request was sent', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->proxy->assertNothingSent();
    });

    it('fake throws an exception if any request was sent when non was expected', function () {
        $fake = Givebutter::fake([
            GetCampaignResponse::fake(GetCampaignFixture::class),
        ]);

        $fake->campaigns()->create([
            'title' => 'test title',
            'description' => 'test description',
        ]);

        $fake->proxy->assertNothingSent();
    })->throws(ExpectationFailedException::class);
});
