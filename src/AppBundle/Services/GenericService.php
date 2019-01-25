<?php

/*
 * This is general service file,
 */

namespace AppBundle\Services;

use AppBundle\Constants\KeyConstants as Key;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * GeneralService will provide services for the application, in which we can perform DB operations,
 * log information etc.
 */
class GenericService
{
    /*
     * Mongo DB Document manager
     *
     * @var DocumentManager $mongoManager
     */
    private $mongoManager;

    /*
     * LoggerInterface to log message
     *
     * @var LoggerInterface $logger
     */
    private $logger;

    /*
     * TranslatorInterface to use translation in the service
     *
     * @var TranslatorInterface $translator
     */
    private $translator;

    /*
     * @var array
    */
    public static $response = [
        Key::STATUS => Key::FAILED,
        Key::CODE => '',
        Key::MESSAGE => '',
        Key::DATA => []
    ];

    /**
     * GenericService constructor.
     * @param DocumentManager $mongoManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        DocumentManager $mongoManager,
        LoggerInterface $logger,
        TranslatorInterface $translator
    )
    {
        $this->mongoManager = $mongoManager;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Function to get response
     *
     * @param null|string $message
     * @param null|string $code
     * @param string $status
     * @param array $data
     * @return array
     */
    public static function getResponse($message = null, $code = null, $status = '', $data = [])
    {
        if ($message) self::$response[Key::MESSAGE] = $message;
        if ($code) self::$response[Key::CODE] = $code;
        if ($status) self::$response[Key::STATUS] = $status;
        if ($data) self::$response[Key::DATA] = $data;

        return self::response();
    }

    /**
     * It will return and reset the response data.
     *
     * @return array
     */
    private static function response()
    {
        $response = self::$response;
        self::$response = [Key::STATUS => Key::FAILED, Key::CODE => '', Key::MESSAGE => '', Key::DATA => []];
        return $response;
    }


}