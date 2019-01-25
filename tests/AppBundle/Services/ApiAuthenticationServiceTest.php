<?php

/*
 * This file is for Api authentication
 */

namespace Tests\AppBundle\Services;

use AppBundle\Services\ApiAuthenticationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * ApiAuthenticationServiceTest is test class for  Api authentication services.
 */
class ApiAuthenticationServiceTest extends WebTestCase
{
    /**
     * @var ApiAuthenticationService
     */
    private static $apiUthenticator;

    /**
     * Set api authentication service to test
     */
    public static function setUpBeforeClass()
    {
        // Boot kernel
        self::bootKernel();

        // Load container
        $container = self::$kernel->getContainer();

        // Load authentication service
        self::$apiUthenticator = $container->get('api.authentication_service');
    }

    /**
     * Test ApiAuthenticationService object, if it is created correctly
     */
    public function testApiAUthenticatorCreated()
    {
        $this->assertInstanceOf(ApiAuthenticationService::class, self::$apiUthenticator);
    }

    /**
     * Function to test authentication
     *
     * @throws \Exception
     */
    public function testCheckAuthentication()
    {
        $expected = [
            'code' => 400,
            'status' => 'failed',
            'message' => 'User is not authenticated.'
        ];

        $request = new Request();
        $request->headers->set('session_id', 'oX4w3KZtXL9Pp6dlpZZ11orAwbDN2cHk7U4fnehSnTcq');
        $request->headers->set('email', 'shams@gmail.com');

        $this->assertEquals($expected, self::$apiUthenticator->checkAuthentication($request));
    }
}