<?php

namespace Fld3\PassportPgtClient\Http\Controllers;

use Fld3\PassportPgtClient\Abstracts\BaseAuthClientController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AuthClientController
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class AuthClientController extends BaseAuthClientController
{
    /**
     * Login
     *
     * @group Authentication
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $log = passportPgtClient()->login($request->get('username'), $request->get('password'));

        return customResponse()
            ->code($log->status)
            ->data($log->data)
            ->message('Successfully logged in.')
            ->generate();
    }

    /**
     * Logout
     *
     * @group Authentication
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $log = passportPgtClient()->logout($request->bearerToken());

        return response()->json($log->data, $log->status);
    }

    /**
     * Refresh Token
     *
     * @group Authentication
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $log = passportPgtClient()->refreshToken($request->get('refresh_token'));

        return customResponse()
            ->code($log->status)
            ->data($log->data)
            ->message('Successfully refreshed tokens.')
            ->generate();
    }

    /**
     * Refresh Token
     *
     * @group Authentication
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $log = passportPgtClient()->getSelf($request->bearerToken());

        return response()->json($log->data, $log->status);
    }
}
