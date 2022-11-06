<?php

/**
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */

use Fld3\PassportPgtClient\Services\PassportPgtClient;

if (! function_exists('passportPgtClient')) {
    /**
     * @param  string|null  $auth_client_controller_class
     * @return PassportPgtClient
     */
    function passportPgtClient(string $auth_client_controller_class = null): PassportPgtClient
    {
        return resolve('passport-pgt-client', [
            'auth_client_controller' => $auth_client_controller_class,
        ]);
    }
}

if (! function_exists('passport_pgt_client')) {
    /**
     * @param  string|null  $auth_client_controller_class
     * @return PassportPgtClient
     */
    function passport_pgt_client(string $auth_client_controller_class = null): PassportPgtClient
    {
        return passportPgtClient($auth_client_controller_class);
    }
}
