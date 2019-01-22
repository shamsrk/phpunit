<?php
/**
 * Created by PhpStorm.
 * User: shams
 * Date: 21/1/19
 * Time: 11:20 AM
 */

namespace Tests\AppBundle\Constants;


class SetupConstants
{
    public static $signupData = [
        'name' => 'Shams',
        'email' => 'shamsaq1@gmail.com',
        'phone' => '8712164261',
        'address' => 'Patia, Bhubaneswar',
        'password' => '12345',
        'dob' => '07-12-2019'
    ];

    public static $signupExpected = [
        'code' => 409,
        'status' => 'failed',
        'message' => 'User already exists.'
    ];



}