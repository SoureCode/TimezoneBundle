<?php

namespace SoureCode\Bundle\Timezone;

use SoureCode\Bundle\Timezone\EventListener\TimezoneListener;
use SoureCode\Bundle\Timezone\Manager\TimezoneManager;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Intl\Timezones;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class SoureCodeTimezoneBundle extends AbstractBundle
{
    private static string $PREFIX = 'soure_code.timezone.';

    public function configure(DefinitionConfigurator $definition): void
    {
        $timezones = ['UTC', ...Timezones::getIds()];

        // @formatter:off
        $definition->rootNode()
            ->fixXmlConfig('timezone')
            ->children()
                ->scalarNode('default_timezone')
                    ->defaultValue('UTC')
                    ->info('The default timezone.')
                    ->validate()
                        ->ifTrue(fn ($v) => !in_array($v, $timezones, true))
                        ->thenInvalid('The timezone "%s" is not valid.')
                    ->end()
                ->end()
                ->arrayNode('enabled_timezones')
                    ->scalarPrototype()->end()
                    ->info('List of enabled timezones. If empty, all timezones are enabled.')
                    ->validate()
                        ->ifTrue(fn (array $values) => count(array_diff($values, $timezones)) > 0)
                        ->then(fn (array $values) => throw new \InvalidArgumentException(sprintf('The timezones "%s" are not valid.', implode('", "', array_diff($values, $timezones)))))
                    ->end()
                ->end()
            ->end()
            ;
        // @formatter:on
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $parameters = $container->parameters();

        $parameters->set(self::$PREFIX . 'default_timezone', $config['default_timezone']);
        $parameters->set(self::$PREFIX . 'enabled_timezones', $config['enabled_timezones']);

        $services = $container->services();

        $services->set(self::$PREFIX . 'manager', TimezoneManager::class)
            ->args([
                param(self::$PREFIX . 'enabled_timezones'),
                service('clock')->ignoreOnInvalid(),
                service('twig')->ignoreOnInvalid(),
            ]);

        $services->alias(TimezoneManager::class, self::$PREFIX . 'manager')
            ->public();

        $services
            ->set(self::$PREFIX.'listener', TimezoneListener::class)
            ->args([
                service(self::$PREFIX.'manager'),
                service('request_stack'),
                service('router')->ignoreOnInvalid(),
                param(self::$PREFIX.'default_timezone'),
            ])
            ->tag('kernel.event_subscriber');
    }
}

