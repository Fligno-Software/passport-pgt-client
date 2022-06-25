<?php

namespace Fld3\PassportPgtClient\Facades;

use Illuminate\Support\Facades\Facade;

class PassportPgtClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'passport-pgt-client';
    }
}
