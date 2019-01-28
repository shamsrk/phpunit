<?php
/**
 * File to test User entity functions
 */

namespace AppBundle\Document;

use PHPUnit\Framework\TestCase;
/**
 * UserTest class to test the User entity
 */
class UserTest extends TestCase
{
    private static $user;

    public static function setUpBeforeClass()
    {
        self::$user = new User();
   }

    public function testUserCreated()
    {
        $this->assertInstanceOf(User::class, self::$user);
   }

   public function userDataProvider() {
        return [
            [
                'setter' => 'setRoles',
                'getter' => 'getRoles',
                'data' => ['ROLE_ADMIN'],
                'expected' => ['ROLE_USER', 'ROLE_ADMIN'],
                'severity' => 'equal'
            ],
            [
                'setter' => 'setName',
                'getter' => 'getName',
                'data' => 'Shams',
                'expected' => 'Shams',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setEmail',
                'getter' => 'getEmail',
                'data' => 'shamsreza@gmail.com',
                'expected' => 'shamsreza@gmail.com',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setUsername',
                'getter' => 'getUsername',
                'data' => 'shamsreza@gmail.com',
                'expected' => 'shamsreza@gmail.com',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setAddress',
                'getter' => 'getAddress',
                'data' => 'Patia, bhubaneswar',
                'expected' => 'Patia, bhubaneswar',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setPhoneNumber',
                'getter' => 'getPhoneNumber',
                'data' => '8712162341',
                'expected' => '8712162341',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setDob',
                'getter' => 'getDob',
                'data' => new \DateTime('2000-01-20'),
                'expected' => new \DateTime('2000-01-20'),
                'severity' => 'date'
            ],
            [
                'setter' => 'setPlainPassword',
                'getter' => 'getPlainPassword',
                'data' => '12345',
                'expected' => '12345',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setPassword',
                'getter' => 'getPassword',
                'data' => 'a34sffw23fsfsda24sd34qwf45wdwe12323rdqwd',
                'expected' => 'a34sffw23fsfsda24sd34qwf45wdwe12323rdqwd',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setCreatedAt',
                'getter' => 'getCreatedAt',
                'data' => new \DateTime(),
                'expected' => new \DateTime(),
                'severity' => 'date'
            ],
            [
                'setter' => 'setUpdatedAt',
                'getter' => 'getUpdatedAt',
                'data' => new \DateTime(),
                'expected' => new \DateTime(),
                'severity' => 'date'
            ],
            [
                'setter' => 'setLastActiveAt',
                'getter' => 'getLastActiveAt',
                'data' => new \DateTime(),
                'expected' => new \DateTime(),
                'severity' => 'date'
            ],
            [
                'setter' => 'setSessionId',
                'getter' => 'getSessionId',
                'data' => '122sdrdd43ws',
                'expected' => '122sdrdd43ws',
                'severity' => 'equal'
            ],
            [
                'setter' => 'setDeviceId',
                'getter' => 'getDeviceId',
                'data' => '12334566',
                'expected' => '12334566',
                'severity' => 'equal'
            ]
        ];
   }

    /**
     * Function to test the User setters and getters functions
     *
     * @dataProvider userDataProvider
     */
    public function testUserSetters($setter, $getter, $data, $expected, $severity)
    {
        if (!empty($setter) && !empty($data)) {
            $this->assertInstanceOf(User::class, self::$user->$setter($data));
        }

        if (!empty($getter) && !empty($expected)) {
            if ($severity == 'date') {
                $this->assertEquals($expected->format('d-m-Y'), self::$user->$getter()->format('d-m-Y'));
            } else {
                $this->assertEquals($expected, self::$user->$getter());
            }
        }
   }
}