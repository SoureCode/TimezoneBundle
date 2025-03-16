<?php

namespace App\Controller;

use SoureCode\Bundle\Timezone\Manager\TimezoneManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TimezoneManager $timezoneManager): JsonResponse
    {
        return $this->json([$timezoneManager->getTimezone()->getName()]);
    }

    #[Route('/override', name: 'app_override', defaults: [
        '_timezone' => 'Europe/Berlin',
    ])]
    public function override(TimezoneManager $timezoneManager): JsonResponse
    {
        return $this->json([$timezoneManager->getTimezone()->getName()]);
    }

    #[Route('/set', name: 'app_set')]
    public function set(
        Request $request,
        TimezoneManager $timezoneManager
    ): JsonResponse
    {
        $previousTimezone = $timezoneManager->getTimezone()->getName();
        $timezone = $request->query->get('_timezone', null);

        if ($timezone) {
            $session = $request->getSession();
            $session->set('_timezone', $timezone);
            $session->save();

            $timezoneManager->setTimezone($timezone);
        }

        return $this->json([
            'previous' => $previousTimezone,
            'current' => $timezoneManager->getTimezone()->getName(),
        ]);
    }
}