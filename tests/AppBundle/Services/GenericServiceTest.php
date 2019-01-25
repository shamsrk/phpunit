<?php
/**
 * Generic service test
 */

namespace Tests\AppBundle\Services;

use AppBundle\Services\GenericService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * GenericServiceTest class
 */
class GenericServiceTest extends WebTestCase
{
    /**
     *
     */
    public function testGenericSericeCreated()
    {
        // start the symfony kernel
        self::bootKernel();

        $container = self::$kernel->getContainer();

        // Get GenericService
        $genericService = $container->get('general_service');

        $this->assertInstanceOf(GenericService::class, $genericService);

    }

    /**
     * Response data provider
     *
     * @return array
     */
    public function responseDataProvider()
    {
        return [
            [
                'expectedResponse' => [
                    'status' => 'failed',
                    'code' => '',
                    'message' => '',
                    'data' => []
                ],
                'responseData' => [
                    'message' => ''
                ]
            ],
            [
                'expectedResponse' => [
                    'status' => 'pass',
                    'code' => '200',
                    'message' => 'This is a test response',
                    'data' => []
                ],
                'responseData' => [
                    'status' => 'pass',
                    'code' => '200',
                    'message' => 'This is a test response',
                    'data' => []
                ]
            ],
            [
                'expectedResponse' => [
                    'status' => 'failed',
                    'code' => '300',
                    'message' => 'This is a failed status test response',
                    'data' => []
                ],
                'responseData' => [
                    'code' => '300',
                    'message' => 'This is a failed status test response',
                    'data' => []
                ]
            ]
        ];
    }

    /**
     * Test function for getResponse
     *
     * @dataProvider responseDataProvider
     */
    public function testGetResponse($expectedResponse, $responseData)
    {
        foreach ($responseData as $key => $value) {
            GenericService::$response[$key] = $value;
        }

        $this->assertEquals($expectedResponse, GenericService::getResponse());
    }
}