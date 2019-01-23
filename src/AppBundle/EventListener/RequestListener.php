<?php
/**
 * File to listen request events
 */

namespace AppBundle\EventListener;

use AppBundle\Services\ApiAuthenticationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/*
 * RequestListener class to listen all the request and handle accordingly
 */
class RequestListener
{
    /*
     *  @var ApiAuthenticationService $authenticator
     */
    private $authenticator;

    /*
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * RequestListener constructor.
     * @param ApiAuthenticationService $authenticator
     * @param LoggerInterface $logger
     */
    public function __construct(ApiAuthenticationService $authenticator, LoggerInterface $logger)
    {
        $this->authenticator = $authenticator;
        $this->logger = $logger;
    }

    /**
     * Function for api request authorization.
     *
     * @param GetResponseEvent $event
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $this->logger->notice($request);

        $requestUri = ltrim($request->getRequestUri(), '/');

        // If "user" is present at the starting in the request uri, then it must be authenticated
        if (strpos($requestUri, 'user') === 0 &&
            ($r = $this->authenticator->checkAuthentication($request)) !== true) {
            $r = new JsonResponse($r);
            $event->setResponse($r);
            $this->logger->error($r);
        }

        return;
    }
}