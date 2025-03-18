<?php

namespace SoureCode\Bundle\Timezone\Tests\EventListener;

use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\Timezone\SoureCodeTimezoneBundle;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class TimezoneListenerTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->setTestProjectDir(__DIR__ . '/../app');
        $kernel->addTestBundle(SoureCodeTimezoneBundle::class);
        $kernel->addTestConfig(__DIR__ . '/../app/config/config.yml');
        $kernel->addTestRoutingFile(__DIR__ . '/../app/config/routes.yml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testDefaultTimezone(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/');
        $response = $client->getResponse();

        // Assert
        self::assertResponseStatusCodeSame(200, 'The response is not successful.');
        self::assertSame('["Asia\/Tokyo"]', $response->getContent(), 'The timezone is not set correctly.');
    }

    public function testOverrideDefaultTimezone(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/override');
        $response = $client->getResponse();

        // Assert
        self::assertResponseStatusCodeSame(200, 'The response is not successful.');
        self::assertSame('["Europe\/Berlin"]', $response->getContent(), 'The timezone is not set correctly.');
    }
}