<?php

/*
 * RegistrationControllerTest file to test Registration PAI
 */

namespace Tests\AppBundle\Controller\users;

use AppBundle\Document\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * RegistrationControllerTest is a test class to perform test cases for user basic actions
 */
class RegistrationControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private static $client;

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
    }

    /**
     * Sign up data provider for testing sign up action
     *
     * @return array
     */
    public function signupDataProvider()
    {
        return [
            [
                'data' => [
                    'name' => 'Shams',
                    'email' => 'shamsaq1@gmail.com',
                    'phone' => '8712164261',
                    'address' => 'Patia, Bhubaneswar',
                    'password' => '12345',
                    'dob' => '07-12-2019'
                ],
                'expected' => [
                    'code' => 406,
                    'status' => 'failed',
                    'message' => 'User data validation failed.',
                    'data' => [
                        'username' => [
                            'username field is required.'
                        ]
                    ]
                ]
            ],
            [
                'data' => [
                    'name' => 'Shams',
                    'email' => 'shamsaq1@gmail.com',
                    'username' => 'shamsaq1@gmail.com',
                    'phone' => '8712164261',
                    'address' => 'Patia, Bhubaneswar',
                    'password' => '12345',
                    'dob' => '07-12-2019'
                ],
                'expected' => [
                    'code' => 200,
                    'status' => 'pass',
                    'message' => 'Successfully singed up.',
                    'data' => [
                        'name' => 'Shams',
                        'email' => 'shamsaq1@gmail.com',
                        'phone' => '8712164261',
                        'address' => 'Patia, Bhubaneswar',
                        'dob' => '07-12-2019',
                        'username' => 'shamsaq1@gmail.com'
                    ]
                ]
            ],
            [
                'data' => [
                    'name' => 'Shams',
                    'email' => 'shamsaq1@gmail.com',
                    'username' => 'shamsaq1@gmail.com',
                    'phone' => '8712164261',
                    'address' => 'Patia, Bhubaneswar',
                    'password' => '12345',
                    'dob' => '07-12-2019'
                ],
                'expected' => [
                    'code' => 409,
                    'status' => 'failed',
                    'message' => 'User already exists.',
                ]
            ]
        ];
    }

    /**
     * Function to test the sign up api
     *
     * @dataProvider signupDataProvider
     */
    public function testSignUpAction($data, $expected)
    {
        self::$client->request('post', '/signup', $data);

        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        $response = json_decode(self::$client->getResponse()->getContent(), true);

        if ($response['code'] == 200) {
            $this->assertEquals($expected['status'], $response['status']);
            $this->assertEquals($expected['message'], $response['message']);

            $this->assertArrayHasKey('name', $response['data']);
            $this->assertArrayHasKey('email', $response['data']);
            $this->assertArrayHasKey('username', $response['data']);
            $this->assertArrayHasKey('phone', $response['data']);
            $this->assertArrayHasKey('address', $response['data']);
            $this->assertArrayHasKey('dob', $response['data']);
        } else {
            $this->assertEquals($expected, $response);
        }
    }

    /**
     * tearDownAfterClass, to remove all the new insertions
     */
    public static function tearDownAfterClass()
    {
        $users = self::$mongoDbConnection->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            self::$mongoDbConnection->remove($user);
        }

        self::$mongoDbConnection->flush();
    }
}