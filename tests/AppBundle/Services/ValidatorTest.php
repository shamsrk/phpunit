<?php

namespace Tests\AppBundle\Services;

use AppBundle\Services\Validator;
use PHPUnit\Framework\TestCase;
use AppBundle\Constants\KeyConstants as Key;

 /*
  * Validator test
 */
class ValidatorTest extends TestCase
{
    /**
     * validator object
     */
    protected $validator;

    /**
     * Signup data provider
     *
     * @return array
     */
    public function signupDataProvider()
    {
        return [
            [
                'signupData' => [
                    Key::NAME => 'Shams',
                    Key::EMAIL => 'shamsaq1@gmail.com',
                    Key::PHONE => '8712164261',
                    Key::ADDRESS => 'Patia, Bhubaneswar',
                    Key::getPasswordKey() => '12345',
                    Key::DOB => '07-12-2019'
                ],
                'signupRules' => [
                    Key::NAME => 'required|max:255|min:5',
                    Key::EMAIL => 'required|max:255',
                    Key::PHONE => 'digits:10',
                    Key::ADDRESS => 'string|max:255',
                    Key::getPasswordKey() => 'required|string|max:25|min:5'
                ],
                'expected' => [
                    'errorStatus' => false,
                    'errors' => []
                ]
            ],
            [
                'signupData' => [
                    Key::EMAIL => 'shamsaq1@gmail.com',
                ],
                'signupRules' => [
                    Key::NAME => 'required|max:255|min:5',
                ],
                'expected' => [
                    'errorStatus' => true,
                    'errors' => ['name' => 'name field is required.']
                ]
            ],
            [
                'signupData' => [
                    Key::EMAIL => 'sham@gmail.com',
                ],
                'signupRules' => [
                    Key::EMAIL => 'required|max:255|min:15',
                ],
                'expected' => [
                    'errorStatus' => true,
                    'errors' => ['email' => 'The length of the field must be greater than 15.']
                ]
            ],
        ];
    }

    /**
     * Function to test the data validation
     *
     * @dataProvider signupDataProvider
     */
    public function testValidate($signupData, $signupRules, $expected)
    {
        // Test all the validations are pass
        $this->validator = Validator::validate($signupData, $signupRules);

        $this->assertEquals($expected['errorStatus'], $this->validator->fails());
        $this->assertEquals($expected['errors'], $this->validator->validationErrors());
    }

    /**
     * teardown fixture
     */
    public function tearDown()
    {
        // Reset the Validator to it's original state after each call
        $this->validator->__destruct();
    }
}