<?php

/*
 * User controller file to test UserController
 */

namespace Tests\AppBundle\Controller\users;

use AppBundle\Document\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * UserControllerTest is a test class to perform test cases for user basic actions
 */
class UserControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private static $client;

    /**
     * @var Client
     */
    private static $mongoDbConnection;

    /**
     * set up before class initiate.
     */
    public static function setUpBeforeClass()
    {
        // Boot kernel
        self::bootKernel();

        // Load container
        $container = self::$kernel->getContainer();

        self::$client = static::createClient();

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
            ->setAddress($userData['address'])
            ->setDob($userData['dob'])
            ->setLastActiveAt($userData['lastActiveAt'])
            ->setSessionId($userData['sessionId']);

        self::$mongoDbConnection->persist($user);
        self::$mongoDbConnection->flush();
    }

    /**
     * User details data provider
     *
     * @return array
     */
    public function userDetailHeaderProvider()
    {
        return [
            [
                'headers' => [
                    'HTTP_email' => 'shams@gmail.com',
                    'HTTP_session_id' => 'oX4w3KZtXL9Pp6dlpZZ11orAwbDN2cHk7U4fnehSnTc'
                ],
                'expected' => [
                    'code' => 400,
                    'status' => 'failed',
                    'message' => 'User is not authenticated.'
                ]
            ],
            [
                'headers' => [
                    'HTTP_email' => 'shams@gmail.com',
                    'HTTP_session_id' => 'y_ioCYS4ZdxxoeeKP3iM_RTqLgSuYuEZZUt0ZYrSX1w'
                ],
                'expected' => [
                    'code' => 200,
                    'status' => 'pass',
                    'message' => 'Request successfull for userDetailsAction.',
                    'date' => [
                        'id' => '',
                        'name' => 'Shams',
                        'email' => 'shams@gmail.com',
                        'username' => 'shams@gmail.com',
                        'phoneNumber' => '8712164261',
                        'address' => 'Patia',
                        'dob' => '',
                        'sessionId' => 'y_ioCYS4ZdxxoeeKP3iM_RTqLgSuYuEZZUt0ZYrSX1w',
                        'lastActiveAt' => ''
                    ]
                ]
            ]
        ];
    }

    /**
     * Test funtion to get user details
     *
     * @dataProvider userDetailHeaderProvider
     */
    public function testUserDetailsAction($headers, $expected)
    {
        self::$client->request(
            'get',
            '/user/details',
            [],
            [],
            $headers
        );

        $response = json_decode(self::$client->getResponse()->getContent(), true);

        if ($response['code'] == 200) {
            $this->assertEquals($expected['status'], $response['status']);
            $this->assertEquals($expected['message'], $response['message']);

            $this->assertArrayHasKey('id', $response['data']);
            $this->assertArrayHasKey('name', $response['data']);
            $this->assertArrayHasKey('email', $response['data']);
            $this->assertArrayHasKey('username', $response['data']);
            $this->assertArrayHasKey('phoneNumber', $response['data']);
            $this->assertArrayHasKey('address', $response['data']);
            $this->assertArrayHasKey('dob', $response['data']);
            $this->assertArrayHasKey('sessionId', $response['data']);
            $this->assertArrayHasKey('lastActiveAt', $response['data']);

            $this->assertNotEmpty($response['data']['sessionId']);
            $this->assertNotEmpty($response['data']['lastActiveAt']);
        } else {
            $this->assertEquals($expected, $response);
        }
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