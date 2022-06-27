<?php

namespace Fld3\PassportPgtClient\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Trait HasAuthClientRefreshTokenTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait HasAuthClientRefreshTokenTrait
{
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
        return customResponse()
            ->data([])
            ->message('Successfully refreshed tokens.')
            ->success()
            ->generate();
    }
}
