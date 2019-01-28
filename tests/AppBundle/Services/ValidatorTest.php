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
                    Key::NAME => 'required|string|max:255|min:5',
                    Key::EMAIL => 'required|max:255',
                    Key::PHONE => 'digits:10',
                    Key::ADDRESS => 'string|max:255',
                    Key::getPasswordKey() => 'required|string|max:25|min:5'
                ],
                'messages' => [],
                'expected' => [
                    'errorStatus' => false,
                    'errors' => []
                ]
            ],
            [
                'signupData' => [
                    Key::EMAIL => 'shamsaq1@gmail.com',
                    Key::ADDRESS => 123,
                    'age' => 'abc',
                    'username' => 'shamsaq1@gmail.com'
                ],
                'signupRules' => [
                    Key::NAME => 'required|max:255|min:5',
                    Key::ADDRESS => 'string|max:500',
                    'age' => 'numeric',
                    'username' => 'required|max:6'
                ],
                'messages' => [
                    'name.required' => 'Please enter your name.',
                    'username.max' => 'Please enter username max 6 characters'
                ],
                'expected' => [
                    'errorStatus' => true,
                    'errors' => [
                        'name' => ['Please enter your name.'],
                        'address' => ['address field must be string.'],
                        'age' => ['age field must be numeric.'],
                        'username' => ['Please enter username max 6 characters']
                    ]
                ]
            ],
            [
                'signupData' => [
                    Key::EMAIL => 'shamgmail.com',
                    Key::PHONE => '87121642',
                    Key::getPasswordKey() => '1234',
                ],
                'signupRules' => [
                    Key::EMAIL => 'email|required|min:15',
                    Key::PHONE => 'digits:10',
                    Key::getPasswordKey() => 'required|string|max:25|min:5',
                    'username' => 'required'
                ],
                'messages' => [],
                'expected' => [
                    'errorStatus' => true,
                    'errors' => [
                        'email' => [
                            'The given email is not a valid email address.',
                            'The length of the field must be greater than 15.'
                        ],
                        'phone' => [
                            'The length of the field must be numeric and equal to 10.'
                        ],
                        'password' => [
                            'The length of the field must be greater than 5.'
                        ],
                        'username' => [
                            'username field is required.'
                        ]
                    ]
                ]
            ],
            [
                'signupData' => [
                    Key::NAME => 123,
                    Key::ADDRESS => 123,
                    Key::EMAIL => 'shamss.com',
                    'age' => 'abc',
                    'username' => 'shamsaq1@gmail.com',
                    'phone' => 'abcsdff'
                ],
                'signupRules' => [
                    Key::NAME => 'required|string|max:255|min:5',
                    Key::ADDRESS => 'string|max:500',
                    Key::EMAIL => 'email|min:15',
                    'age' => 'numeric',
                    'username' => 'required|max:6',
                    'phone' => 'digits:10'
                ],
                'messages' => [
                    'name.string' => 'Name must be a string.',
                    'age.numeric' => 'Please enter a numeric input.',
                    'phone.digits' => 'Please enter correct phone number.',
                    'email.email' => 'please enter a valid email address.',
                    'email.min' => 'Email address must be min of 15 characters.'

                ],
                'expected' => [
                    'errorStatus' => true,
                    'errors' => [
                        'name' => [
                            'Name must be a string.',
                            'The length of the field must be greater than 5.'
                        ],
                        'address' => ['address field must be string.'],
                        'email' => [
                            'please enter a valid email address.',
                            'Email address must be min of 15 characters.'
                        ],
                        'age' => ['Please enter a numeric input.'],
                        'username' => ['The length of the field must be less than 6.'],
                        'phone' => ['Please enter correct phone number.']
                    ]
                ]
            ],
            [
                'signupData' => [
                    Key::NAME => 123,
                ],
                'signupRules' => [
                    Key::NAME => 'required|string|max:ten',
                ],
                [],
                'expected' => 'exception'
            ],
            [
                'signupData' => [
                    Key::NAME => 123,
                ],
                'signupRules' => [
                    Key::NAME => 'required|string|min:ten',
                ],
                [],
                'expected' => 'exception'
            ]
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
    public function testValidate($signupData, $signupRules, $messages, $expected)
    {
        if ($expected == 'exception') {
            $this->expectException(\Exception::class);
        }

        // Test all the validations are pass
        $this->validator = Validator::validate($signupData, $signupRules, $messages);

        $this->assertEquals($expected['errorStatus'], $this->validator->fails());
        $this->assertEquals($expected['errors'], $this->validator->validationErrors());
    }
}