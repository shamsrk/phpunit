<?php
/**
 * File for Error code,severity and error message management.
 */

namespace AppBundle\Constants;


final class ErrorConstants
{
    /*
     * General errors with code and severity
     *
     * @var array
     */
    public static $generalErrors = [
        'ALREADY_EXISTS' => ['code' => 409, 'status' => 'failed', 'message' => 'error.user.already_exists'],
        'NOT_EXISTS' => ['code' => 403, 'status' => 'failed', 'message' => 'error.user.not_exists'],
        'VALIDATION_FAIL' => ['code' => 406, 'status' => 'failed', 'message' => 'error.validation_failed'],
        'INVALID_PASSWORD' => ['code' => 401, 'status' => 'failed', 'message' => 'error.user.invalid_password'],
    ];
}