<?php
/*
 * Key constant file, it contains all the common keys which are being used frequently in the project
 */

namespace AppBundle\Constants;

/**
 * KeyConstants class, list the constant keys for convenient
 */
final class KeyConstants
{
    const KEY = 'key';
    const TRANS_KEY = '%key%';
    const NAME = 'name';
    const EMAIL = 'email';
    const USERNAME = 'username';
    const PHONE = 'phone';
    const ADDRESS = 'address';
    const DOB = 'dob';
    const DATA = 'data';
    const MESSAGE = 'message';
    const ERROR = 'error';
    const CODE = 'code';
    const FAILED = 'failed';
    const STATUS = 'status';
    const REQUIRED = 'required';
    const STRING = 'string';
    const NUMERIC = 'numeric';
    const DIGITS = 'digits';
    const MAX = 'max';
    const MIN = 'min';
    const LOCALE = 'locale';
    const INCORRECT = 'incorrect';
    const SESSION_ID = 'session_id';
    const DEVICE_ID = 'device_id';
    const DOCTRINE_MONGODB = 'doctrine_mongodb';

    /**
     * Get password key
     *
     * @return string
     */
    public static function getPasswordKey()
    {
        return 'password';
    }
}