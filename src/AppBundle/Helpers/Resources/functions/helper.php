<?php
/**
 * This script helps with helper functions
 */

use AppBundle\Helpers\Helpers;

if (!function_exists('generateToken')) {
    /**
     * Function to create random token
     */
    function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
