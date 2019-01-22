<?php
/**
 * Created by PhpStorm.
 * User: shams
 * Date: 17/1/19
 * Time: 1:10 PM
 */

namespace AppBundle\Constants;


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

    public static function getPasswordKey(){
        return 'password';
    }
}