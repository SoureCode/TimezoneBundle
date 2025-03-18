<?php

namespace SoureCode\Bundle\Timezone\Manager;

use DateTimeZone;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Intl\Timezones;
use Twig\Environment;
use Twig\Extension\CoreExtension;

class TimezoneManager
{
    private static TimezoneManager $instance;
    private DateTimeZone $timezone;

    public function __construct(
        private array $enabledTimezoneNames = [],
        private readonly ?ClockInterface $clock = null,
        private readonly ?Environment $twig = null,
    )
    {
        if (empty($this->enabledTimezoneNames)) {
            $this->enabledTimezoneNames = Timezones::getIds();
        }

        $this->timezone = new DateTimeZone('Etc/UTC');

        self::setInstance($this);
    }

    private static function setInstance(self $timezone): void
    {
        self::$instance = $timezone;
    }

    public static function getInstance(): TimezoneManager
    {
        // in case of getting called from a command or messenger. (console)
        if (!isset(self::$instance)) {
            new self([], Clock::get());
        }

        return self::$instance;
    }

    public function setTimezone(DateTimeZone|string $value): void
    {
        if (is_string($value)) {
            $value = new DateTimeZone($value);
        }

        if (!in_array($value->getName(), $this->enabledTimezoneNames, true)) {
            throw new \InvalidArgumentException(sprintf('The timezone "%s" is not enabled.', $value->getName()));
        }

        $this->timezone = $value;

        $this->clock?->withTimeZone($this->timezone);
        $this->twig?->getExtension(CoreExtension::class)->setTimezone($this->timezone);

        date_default_timezone_set($this->timezone->getName());
    }

    public function getTimezone(): DateTimeZone
    {
        return $this->timezone;
    }

    public function getEnabledTimezoneNames(): array
    {
        return $this->enabledTimezoneNames;
    }
}