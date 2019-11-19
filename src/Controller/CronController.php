<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @Route("/cron/cc", name="cron_clear_cache", methods={"GET"})
     * @return JsonResponse
     * @throws Exception
     */
    public function clearCache(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $application->run(new ArrayInput([
            'command' => 'cache:clear',
            '--env' => $kernel->getEnvironment(),
        ]));

        return new JsonResponse(['message' => 'OK']);
    }

    /**
     * @Route("/cron/migrate", name="cron_migrate", methods={"GET"})
     * @return JsonResponse
     * @throws Exception
     */
    public function migrate(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $application->run(new ArrayInput([
            'command' => 'doctrine:migration:migrate',
            '--allow-no-migration' => '',
            '--no-interaction' => ''
        ]));

        return new JsonResponse(['message' => 'OK']);
    }
}
