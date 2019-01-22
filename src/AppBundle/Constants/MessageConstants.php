<?php
/**
 * File for Message code,info message management.
 */
namespace AppBundle\Constants;

final class MessageConstants
{
    /*
  * General info messages with code and status
  *
  * @var array
  */
    public static $generalInfo = [
        'SIGNUP_SUCCESS' => ['code' => 200, 'status' => 'pass', 'message' => 'info.user.signup_success'],
        'SIGNIN_SUCCESS' => ['code' => 200, 'status' => 'pass', 'message' => 'info.user.signin_success'],
    ];
}