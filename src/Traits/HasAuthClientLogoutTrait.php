<?php

namespace Fld3\PassportPgtClient\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Trait HasAuthClientLogoutTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait HasAuthClientLogoutTrait
{
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
        return customResponse()
            ->data([])
            ->message('Successfully logged out.')
            ->success()
            ->generate();
    }
}
