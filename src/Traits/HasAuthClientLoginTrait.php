<?php

namespace Fld3\PassportPgtClient\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Trait HasAuthClientLoginTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait HasAuthClientLoginTrait
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
        return customResponse()
            ->data([])
            ->message('Successfully logged in.')
            ->success()
            ->generate();
    }
}
