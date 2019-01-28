<?php

/*
 * This file is for Api authentication
 */

namespace Tests\AppBundle\Services;

use AppBundle\Document\User;
use AppBundle\Services\ApiAuthenticationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * ApiAuthenticationServiceTest is test class for  Api authentication services.
 */
class ApiAuthenticationServiceTest extends WebTestCase
{
    /**
     * @var ApiAuthenticationService
     */
    private static $apiUthenticator;

    /*
     * Mongo DB Document manager
     *
     * @var DocumentManager $mongoDbConnection
     */
    private static $mongoDbConnection;

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

        self::$mongoDbConnection = $container->get('doctrine_mongodb')
            ->getManager();

        // remove all the records from User collection
        $users = self::$mongoDbConnection->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            self::$mongoDbConnection->remove($user);
        }

        self::$mongoDbConnection->flush();

        // create user data to test user_details API
        $userData = [
            'name' => 'Shams',
            'email' => 'shams@gmail.com',
            'username' => 'shams@gmail.com',
            'password' => '$2y$13$NgkDG6KPvKjXHTTw3nZFbuEd6KVgv.6wwVgqAvTIF.X8WyAFM2SQe', // asbcd
            'phoneNumber' => '8712164261',
            'address' => 'Patia',
            'dob' => '10-11-2000',
            'sessionId' => 'y_ioCYS4ZdxxoeeKP3iM_RTqLgSuYuEZZUt0ZYrSX1w',
            'lastActiveAt' => new \DateTime()
        ];

        $user = new User();
        $user->setUsername($userData['username'])
            ->setEmail($userData['email'])
            ->setName($userData['name'])
            ->setPassword($userData['password'])
            ->setPhoneNumber($userData['phoneNumber'])
            ->setAddress($userData['address'])
            ->setDob($userData['dob'])
            ->setLastActiveAt($userData['lastActiveAt'])
            ->setSessionId($userData['sessionId']);

        self::$mongoDbConnection->persist($user);
        self::$mongoDbConnection->flush();
    }

    /**
     * Test ApiAuthenticationService object, if it is created correctly
     */
    public function testApiAUthenticatorCreated()
    {
        $this->assertInstanceOf(ApiAuthenticationService::class, self::$apiUthenticator);
    }

    /**
     * @return array
     */
    public function authDataProvider()
    {
        return [
            [
                'data' => [
                    'session_id' => 'oX4w3KZtXL9Pp6dlpZZ11orAwbDN2cHk7U4fnehSnTcq',
                    'email' => 'abcd@gmail.com'
                ],
                'expected' => [
                    'code' => 400,
                    'status' => 'failed',
                    'message' => 'User is not authenticated.'
                ]
            ],
            [
                'data' => [
                    'email' => 'abcd@gmail.com'
                ],
                'expected' => [
                    'code' => 400,
                    'status' => 'failed',
                    'message' => 'request headers are incorrect.'
                ]
            ],
            [
                'data' => [
                    'session_id' => 'y_ioCYS4ZdxxoeeKP3iM_RTqLgSuYuEZZUt0ZYrSX1w',
                    'email' => 'shams@gmail.com'
                ],
                'expected' => true
            ]
        ];
    }

    /**
     * Function to test authentication
     * @dataProvider authDataProvider
     * @throws \Exception
     */
    public function testCheckAuthentication($data, $expected)
    {
        $request = new Request();

        foreach ($data as $k => $v) {
            $request->headers->set($k, $v);
        }

        $this->assertEquals($expected, self::$apiUthenticator->checkAuthentication($request));
    }


    /**
     * Remove all the inserted data
     */
    public static function tearDownAfterClass()
    {
        // remove all the records from User collection
        $users = self::$mongoDbConnection->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            self::$mongoDbConnection->remove($user);
        }

        self::$mongoDbConnection->flush();
    }
}