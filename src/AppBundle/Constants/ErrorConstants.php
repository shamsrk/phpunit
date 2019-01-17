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
        'ALREADYEXISTS' => ['code' => 409, 'status' => 'failed', 'message' => 'error.user.already_exists'],
        'VALIDATIONFAIL' => ['code' => 406, 'status' => 'failed', 'message' => 'error.validation_failed'],
    ];
}