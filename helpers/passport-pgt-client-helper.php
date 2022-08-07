<?php

/**
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */

use Fld3\PassportPgtClient\PassportPgtClient;

if (! function_exists('passportPgtClient')) {
    /**
     * @param  string|null  $authClientControllerClass
     * @return PassportPgtClient
     */
    function passportPgtClient(string $authClientControllerClass = null): PassportPgtClient
    {
        return resolve('passport-pgt-client', [
            'auth_client_controller' => $authClientControllerClass,
        ]);
    }
}

if (! function_exists('passport_pgt_client')) {
    /**
     * @param  string|null  $authClientControllerClass
     * @return PassportPgtClient
     */
    function passport_pgt_client(string $authClientControllerClass = null): PassportPgtClient
    {
        return passportPgtClient($authClientControllerClass);
    }
}
