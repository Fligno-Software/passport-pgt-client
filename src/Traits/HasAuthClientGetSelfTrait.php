<?php

namespace Fld3\PassportPgtClient\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Trait HasAuthClientGetSelfTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait HasAuthClientGetSelfTrait
{
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
        return customResponse()
            ->data($request->user())
            ->message('Successfully collected record.')
            ->success()
            ->generate();
    }
}
