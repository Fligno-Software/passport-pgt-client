<?php

namespace Fld3\PassportPgtClient\Abstracts;

use App\Http\Controllers\Controller;
use Fld3\PassportPgtClient\Traits\HasAuthClientGetSelfTrait;
use Fld3\PassportPgtClient\Traits\HasAuthClientLoginTrait;
use Fld3\PassportPgtClient\Traits\HasAuthClientLogoutTrait;
use Fld3\PassportPgtClient\Traits\HasAuthClientRefreshTokenTrait;

/**
 * Class BaseAuthClientController
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
abstract class BaseAuthClientController extends Controller
{
    use HasAuthClientLoginTrait;
    use HasAuthClientLogoutTrait;
    use HasAuthClientRefreshTokenTrait;
    use HasAuthClientGetSelfTrait;
}
