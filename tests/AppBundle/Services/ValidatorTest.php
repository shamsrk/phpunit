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
                    'errors' => ['name' => ['name field is required.']]
                ]
            ],
            [
                'signupData' => [
                    Key::EMAIL => 'shamgmail.com',
                    Key::PHONE => '8712164261',
                    Key::getPasswordKey() => '1234'
                ],
                'signupRules' => [
                    Key::EMAIL => 'email|required|min:15',
                    Key::PHONE => 'digits:10',
                    Key::getPasswordKey() => 'required|string|max:25|min:5'
                ],
                'expected' => [
                    'errorStatus' => true,
                    'errors' => [
                        'email' => [
                            'The given email is not a valid email address.',
                            'The length of the field must be greater than 15.'
                        ],
                        'password' => [
                            'The length of the field must be greater than 5.'
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * Test Validator service multiple behaviours
     */
    public function testValidator()
    {
        $this->validator = Validator::getInstance();

        // test validator getInstance
        $this->assertInstanceOf(Validator::class, $this->validator);

        // test validator initial hasError flag
        $this->assertFalse($this->validator->fails());

        // test validator initial errors
        $this->assertEmpty($this->validator->validationErrors());
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