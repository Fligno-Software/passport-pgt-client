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
     * @group Authentication (Client)
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $log = passportPgtClient()->register($request->all());

        if ($log->getStatusFromResponse() == 200 && $log->getDataFromResponse()->count()) {
            return response()->json($log->getDataFromResponse(), $log->getStatusFromResponse());
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
     * @group Authentication (Client)
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $log = passportPgtClient()->login($validated['username'], $validated['password']);

        if ($log->getStatusFromResponse() == 200) {
            return customResponse()
                ->success()
                ->data($log->getDataFromResponse())
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
     * @group Authentication (Client)
     * @authenticated
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $log = passportPgtClient()->logout($request->bearerToken());

        if ($log->getStatusFromResponse() == 200) {
            return response()->json($log->getDataFromResponse(), $log->getStatusFromResponse());
        }

        return customResponse()
            ->failed()
            ->message('Failed to logout user.')
            ->generate();
    }

    /**
     * Refresh Token
     *
     * @group Authentication (Client)
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'refresh_token' => 'required',
        ]);

        $log = passportPgtClient()->refreshToken($validated['refresh_token']);

        if ($log->getStatusFromResponse() == 200) {
            return customResponse()
                ->success()
                ->data($log->getDataFromResponse())
                ->message('Successfully refreshed tokens.')
                ->generate();
        }

        return customResponse()
            ->failed()
            ->message('Failed to refresh token.')
            ->generate();
    }

    /**
     * Get Self
     *
     * @group Authentication (Client)
     * @authenticated
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $log = passportPgtClient()->getSelf($request->bearerToken());

        if ($log->getStatusFromResponse() == 200) {
            return response()->json($log->getDataFromResponse(), $log->getStatusFromResponse());
        }

        return customResponse()
            ->data([])
            ->message('You do not have the necessary permission to access this resource.')
            ->failed(403)
            ->generate();
    }
}
