<?php

namespace Fld3\PassportPgtClient\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DefaultAuthController
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class DefaultAuthController extends Controller
{
    /**
     * Register
     *
     * Register a new user.
     *
     * @group Authentication
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $log = passportPgtClient()->register($request->all());

        if ($log?->isSuccessful()) {
            return response()->json($log->data, $log->status);
        }

        return customResponse()
            ->failed()
            ->message('Failed to create new user.')
            ->generate();
    }

    /**
     * Login
     *
     * Email and password are required to login.
     *
     * @group Authentication
     *
     * @bodyParam username string required The account's username Example: kaylin.kuhlman@example.org
     * @bodyParam password string required The account's password Example: password
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $log = passportPgtClient()->login($request->get('username'), $request->get('password'));

        if ($log?->isSuccessful()) {
            return customResponse()
                ->success()
                ->data($log->data)
                ->message('Successfully logged in.')
                ->generate();
        }

        return customResponse()
            ->failed()
            ->message('Incorrect username or password.')
            ->generate();
    }

    /**
     * Logout
     *
     * @group Authentication
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $log = passportPgtClient()->logout($request->bearerToken());

        if ($log?->isSuccessful()) {
            return response()->json($log->data, $log->status);
        }

        return customResponse()
            ->failed()
            ->message('Failed to logout user.')
            ->generate();
    }

    /**
     * Refresh Token
     *
     * @group Authentication
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $log = passportPgtClient()->refreshToken($request->get('refresh_token'));

        if ($log?->isSuccessful()) {
            return customResponse()
                ->success()
                ->data($log->data)
                ->message('Successfully refreshed tokens.')
                ->generate();
        }

        return customResponse()
            ->failed()
            ->message('Failed to refresh token.')
            ->generate();
    }

    /**
     * Refresh Token
     *
     * @group Authentication
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $log = passportPgtClient()->getSelf($request->bearerToken());

        if ($log?->isSuccessful()) {
            return response()->json($log->data, $log->status);
        }

        return customResponse()
            ->data([])
            ->message('You do not have the necessary permission to access this resource.')
            ->failed(403)
            ->generate();
    }
}
