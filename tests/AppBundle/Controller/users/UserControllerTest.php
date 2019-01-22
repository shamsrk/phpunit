<?php
/**
 * Created by PhpStorm.
 * User: shams
 * Date: 18/1/19
 * Time: 2:40 PM
 */

namespace Tests\AppBundle\Controller\users;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * Funtion to test the signup api
     */
    public function testSignUpAction()
    {
        // Actual data passing to the signup api, change the details to test different cases
        $actual = [
            'name' => 'Shams',
            'email' => 'shamsaq1@gmail.com',
            'phone' => '8712164261',
            'address' => 'Patia, Bhubaneswar',
            'password' => '12345',
            'dob' => '07-12-2019'
        ];

        $client = static::createClient();
        $client->request('post', '/signup', $actual);

        $expected = [
            'code' => 409,
            'status' => 'failed',
            'message' => 'User already exists.'
        ];

//        unset($actual['password']);
//        $expected = [
//            'code' => 200,
//            'status' => 'pass',
//            'message' => 'Successfully singed up.',
//            'data' => $actual
//        ];

        $this->assertJsonStringEqualsJsonString(
            json_encode($expected),
            $client->getResponse()->getContent()
        );
    }

}