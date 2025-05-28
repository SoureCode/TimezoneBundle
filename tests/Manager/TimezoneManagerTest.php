<?php

namespace SoureCode\Bundle\Timezone\Tests\Manager;

use DateInvalidTimeZoneException;
use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\Timezone\EventListener\TimezoneListener;
use SoureCode\Bundle\Timezone\Manager\TimezoneManager;
use SoureCode\Bundle\Timezone\SoureCodeTimezoneBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\HttpKernel\KernelInterface;

class TimezoneManagerTest extends KernelTestCase
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
        $kernel->addTestConfig(__DIR__ . '/../app/config/monolog.yaml');
        $kernel->addTestConfig(__DIR__ . '/../app/config/config.yml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testSomething()
    {
        $manager = self::getContainer()->get('soure_code.timezone.manager');
        $this->assertInstanceOf(TimezoneManager::class, $manager);

        $listener = self::getContainer()->get('soure_code.timezone.listener');
        $this->assertInstanceOf(TimezoneListener::class, $listener);
    }

    public function testDefaultTimezoneIsSetOnInitialization(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /** @var TimezoneManager $timezoneManager */
        $timezoneManager = $container->get(TimezoneManager::class);

        // Act
        $timezone = $timezoneManager->getTimezone();

        // Assert
        $this->assertEquals('Etc/UTC', $timezone->getName());
    }

    public function testSetValidTimezone(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /** @var TimezoneManager $timezoneManager */
        $timezoneManager = $container->get(TimezoneManager::class);

        // Act
        $timezoneManager->setTimezone('Europe/Berlin');
        $timezone = $timezoneManager->getTimezone();

        // Assert
        $this->assertEquals('Europe/Berlin', $timezone->getName());
    }

    public function testSetInvalidTimezoneThrowsException(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /** @var TimezoneManager $timezoneManager */
        $timezoneManager = $container->get(TimezoneManager::class);

        // Act & Assert
        $this->expectException(DateInvalidTimeZoneException::class);
        $timezoneManager->setTimezone('Invalid/Timezone');
    }

    public function testNotEnabledTimezoneThrowsException(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /** @var TimezoneManager $timezoneManager */
        $timezoneManager = $container->get(TimezoneManager::class);

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $timezoneManager->setTimezone('America/New_York');
    }

    public function testSingletonInstance(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /** @var TimezoneManager $timezoneManager */
        $timezoneManager = $container->get(TimezoneManager::class);

        // Act
        $instance = TimezoneManager::getInstance();

        // Assert
        $this->assertSame($timezoneManager, $instance);
    }
}

