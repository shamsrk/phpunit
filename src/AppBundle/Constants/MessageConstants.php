<?php
/**
 * File for Message code,info message management.
 */

namespace AppBundle\Constants;

/**
 * MessageConstants class to list message constants
 */
final class MessageConstants
{
    /**
     * General info messages with code and status
     *
     * @var array
     */
    public static $generalInfo = [
        'REQUEST_SUCCESS' => ['code' => 200, 'status' => 'pass', 'message' => 'info.request_success'],
        'SIGNUP_SUCCESS' => ['code' => 200, 'status' => 'pass', 'message' => 'info.user.signup_success'],
        'SIGNIN_SUCCESS' => ['code' => 200, 'status' => 'pass', 'message' => 'info.user.signin_success'],
    ];
}