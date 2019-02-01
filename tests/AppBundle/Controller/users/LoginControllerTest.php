<?php

/*
 * LoginControllerTest file to test Login API
 */

namespace Tests\AppBundle\Controller\users;

use AppBundle\Document\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * LoginControllerTest is a test class to perform test cases for user basic actions
 */
class LoginControllerTest extends WebTestCase
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

        // create user data to test login API
        $userData = [
            'name' => 'Shams',
            'email' => 'shams@gmail.com',
            'username' => 'shams@gmail.com',
            'password' => '$2y$13$NgkDG6KPvKjXHTTw3nZFbuEd6KVgv.6wwVgqAvTIF.X8WyAFM2SQe', // asbcd
            'phoneNumber' => '8712164261',
            'address' => '',
            'dob' => '10-11-2000',
            'sessionId' => '',
            'lastActiveAt' => ''
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
     * user sign in data provider
     *
     * @return array
     */
    public function signinDataProvider()
    {
        return [
            [
                'data' => [
                    'password' => '12345',
                    'device_id' => '123456'
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
                ],
                'expected' => [
                    'code' => 406,
                    'status' => 'failed',
                    'message' => 'User data validation failed.',
                    'data' => [
                        'username' => [
                            'username field is required.'
                        ],
                        'password' => [
                            'password field is required.'
                        ],
                        'device_id' => [
                            'device_id field is required.'
                        ]
                    ]
                ]
            ],
            [
                'data' => [
                    'username' => '',
                    'password' => '',
                    'device_id' => ''
                ],
                'expected' => [
                    'code' => 406,
                    'status' => 'failed',
                    'message' => 'User data validation failed.',
                    'data' => [
                        'username' => [
                            'username field is required.'
                        ],
                        'password' => [
                            'password field is required.'
                        ],
                        'device_id' => [
                            'device_id field is required.'
                        ]
                    ]
                ]
            ],
            [
                'data' => [
                    'username' => 'shams@gmail.com',
                    'password' => 'asbcd',
                    'device_id' => '123456'
                ],
                'expected' => [
                    'code' => 200,
                    'status' => 'pass',
                    'message' => 'Successfully signed in.',
                    'data' => [
                        'id' => '5c46ca175af99a174c69cbf9',
                        'name' => 'shams',
                        'email' => 'shams@gmail.com',
                        'username' => 'shams@gmail.com',
                        'phoneNumber' => '8712164261',
                        'address' => '',
                        'dob' => '',
                        'sessionId' => 'zi7rs_Uc47obasQXowbEXd7hpCkWkfNMGxxiEfTXNR0',
                        'lastActiveAt' => ''
                    ]
                ]
            ],
            [
                'data' => [
                    'username' => 'shamsreza@gmail.com',
                    'password' => 'asbcd',
                    'device_id' => '123456'
                ],
                'expected' => [
                    'code' => 403,
                    'status' => 'failed',
                    'message' => 'User does not exists.',
                ]
            ]
        ];
    }

    /**
     * user sign in test function
     *
     * @dataProvider signinDataProvider
     */
    public function testLoginAction($data, $expected)
    {
        self::$client->request('post', '/signin', $data);

        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

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