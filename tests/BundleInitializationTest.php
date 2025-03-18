<?php

namespace SoureCode\Bundle\Timezone\Tests;

use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\Timezone\Manager\TimezoneManager;
use SoureCode\Bundle\Timezone\SoureCodeTimezoneBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->setTestProjectDir(__DIR__ . '/app');
        $kernel->addTestBundle(SoureCodeTimezoneBundle::class);
        $kernel->addTestConfig(__DIR__ . '/app/config/config.yml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testInitBundle(): void
    {
        // Boot the kernel.
        $kernel = self::bootKernel();

        $container = $kernel->getContainer();

        $this->assertTrue($container->has(TimezoneManager::class));
    }
}
