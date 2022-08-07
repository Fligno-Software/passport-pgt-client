<?php

namespace Fld3\PassportPgtClient\Traits;

use Illuminate\Routing\Controller;
use RuntimeException;

/**
 * Trait HasAuthMethodsTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait HasAuthMethodsTrait
{
    /**
     * @param  string  $method
     * @param  string  $controller
     * @return bool
     */
    protected function mustHaveMethod(string $method, string $controller): bool
    {
        if (! method_exists($controller, $method)) {
            throw new RuntimeException('Method not found on '.$controller.': '.$method);
        }

        return true;
    }

    /**
     * @param  string  $controller
     * @return bool
     */
    protected function isInvokable(string $controller): bool
    {
        return method_exists($controller, '__invoke');
    }

    /**
     * @param  string  $controller
     * @return bool
     */
    protected function mustBeController(string $controller): bool
    {
        if (! is_subclass_of($controller, Controller::class)) {
            throw new RuntimeException('Must be a subclass of Controller: '.$controller);
        }

        return true;
    }

    /**
     * @param  string  $controller
     * @return bool
     */
    protected function mustHaveLogin(string $controller): bool
    {
        return $this->mustHaveMethod('login', $controller);
    }

    /**
     * @param  string  $controller
     * @return bool
     */
    protected function mustHaveLogout(string $controller): bool
    {
        return $this->mustHaveMethod('logout', $controller);
    }

    /**
     * @param  string  $controller
     * @return bool
     */
    protected function mustHaveGetSelf(string $controller): bool
    {
        return $this->mustHaveMethod('me', $controller);
    }

    /**
     * @param  string  $controller
     * @return bool
     */
    protected function mustHaveRegister(string $controller): bool
    {
        return $this->mustHaveMethod('register', $controller);
    }

    /**
     * @param  string  $controller
     * @return bool
     */
    protected function mustHaveRefreshToken(string $controller): bool
    {
        return $this->mustHaveMethod('refreshToken', $controller);
    }
}
