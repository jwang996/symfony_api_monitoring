<?php

declare(strict_types=1);

namespace App\EventSubscriber\Prometheus;

use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;
use Prometheus\Histogram;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MetricsSubscriber implements EventSubscriberInterface
{
    private Histogram $histogram;
    private array $startTimes = [];

    /**
     * @throws MetricsRegistrationException
     */
    public function __construct(private readonly CollectorRegistry $registry)
    {
        $this->histogram = $this->registry->getOrRegisterHistogram(
            'symfony_app',
            'http_request_duration_seconds',
            'HTTP request duration in seconds',
            ['method', 'route', 'status']
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST   => ['onKernelRequest', 100],
            KernelEvents::TERMINATE => ['onKernelTerminate', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (! $event->isMainRequest()) {
            return;
        }
        $req = $event->getRequest();
        $this->startTimes[spl_object_id($req)] = microtime(true);
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $req = $event->getRequest();
        $res = $event->getResponse();
        $id  = spl_object_id($req);

        $start = $this->startTimes[$id] ?? null;
        unset($this->startTimes[$id]);

        if ($start === null) {
            return;
        }

        $duration = microtime(true) - $start;
        $route    = $req->attributes->get('_route', 'n/a');
        $method   = $req->getMethod();
        $status   = (string) $res->getStatusCode();

        $this->histogram->observe($duration, [$method, $route, $status]);
    }
}
