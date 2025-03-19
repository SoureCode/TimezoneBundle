<?php

namespace SoureCode\Bundle\Timezone\EventListener;

use SoureCode\Bundle\Timezone\Manager\TimezoneManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContextAwareInterface;

final readonly class TimezoneListener implements EventSubscriberInterface
{
    public function __construct(
        private TimezoneManager               $timezoneManager,
        private RequestStack                  $requestStack,
        private ?RequestContextAwareInterface $router = null,
        private string                        $defaultTimezone = 'Etc/UTC',
    )
    {
    }

    public function setDefaultTimezone(KernelEvent $event): void
    {
        $this->timezoneManager->setTimezone($this->defaultTimezone);
    }

    public function onKernelFinishRequest(FinishRequestEvent $event): void
    {
        if (null !== $parentRequest = $this->requestStack->getParentRequest()) {
            $this->setRouterContext($parentRequest);
        }
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $this->setTimezone($request);
        $this->setRouterContext($request);
    }

    private function setTimezone(Request $request): void
    {
        if ($timezone = $request->attributes->get('_timezone')) {
            $this->timezoneManager->setTimezone($timezone);
        } else {
            $request->attributes->set('_timezone', $this->timezoneManager->getTimezone()->getName());
        }

    }

    private function setRouterContext(Request $request): void
    {
        $this->router?->getContext()->setParameter('_timezone', $this->timezoneManager->getTimezone()->getName());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['setDefaultTimezone', 100],
                // must be registered after the Router to have access to the _timezone
                ['onKernelRequest', 16],
            ],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
        ];
    }
}
