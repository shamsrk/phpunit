<?php

/*
 * User controller file to test UserController
 */

namespace Tests\AppBundle\Controller\users;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * UserControllerTest is a test class to perform test cases for user basic actions
 */
class UserControllerTest extends WebTestCase
{
    /**
     * Sign up data provider for testing sign up action
     *
     * @return array
     */
    public function signupDataProvider()
    {
        return [
            [
                'actual' => [
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
                'actual' => [
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
    public function testSignUpAction($actual, $expected)
    {
        $client = static::createClient();
        $client->request('post', '/signup', $actual);

        $this->assertJsonStringEqualsJsonString(
            json_encode($expected),
            $client->getResponse()->getContent()
        );
    }
}