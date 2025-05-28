<?php

namespace SoureCode\Bundle\Timezone\Tests\EventListener;

use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\Timezone\SoureCodeTimezoneBundle;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\MonologBundle\MonologBundle;
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
        $kernel->addTestBundle(MonologBundle::class);
        $kernel->addTestBundle(SoureCodeTimezoneBundle::class);
        $kernel->addTestConfig(__DIR__ . '/../app/config/config.yml');
        $kernel->addTestConfig(__DIR__ . '/../app/config/monolog.yaml');
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
        $request = $client->getRequest();
        $response = $client->getResponse();

        // Assert
        self::assertResponseStatusCodeSame(200, 'The response is not successful.');
        self::assertSame('["Asia\/Tokyo"]', $response->getContent(), 'The timezone is not set correctly.');
        self::assertSame('Asia/Tokyo', $request->attributes->get('_timezone'));
    }

    public function testOverrideDefaultTimezone(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/override');
        $request = $client->getRequest();
        $response = $client->getResponse();

        // Assert
        self::assertResponseStatusCodeSame(200, 'The response is not successful.');
        self::assertSame('["Europe\/Berlin"]', $response->getContent(), 'The timezone is not set correctly.');
        self::assertSame('Europe/Berlin', $request->attributes->get('_timezone'));

    }
}