<?php

namespace Fld3\PassportPgtClient\Traits;

use Fligno\StarterKit\Traits\HasTaggableCacheTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Trait HasAuthMethodsTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait HasAuthMethodsTrait
{
    use HasTaggableCacheTrait;

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

    /**
     * @param  string|null  $method
     * @param  bool  $rehydrate
     * @return Collection|array|string|null
     */
    public function getControllers(string $method = null, bool $rehydrate = false): Collection|array|string|null
    {
        $tags = $this->getTags();
        $key = 'controllers';

        $result = $this->getCache($tags, $key, fn () => collect($this->controllers), $rehydrate);

        if ($method) {
            return $result->get($method);
        }

        return $result;
    }

    /**
     * @param  string  $method
     * @param  string  $controller
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    protected function setController(string $method, string $controller, bool $override = false, bool $throw_error = true): bool
    {
        if ($this->mustBeController($controller)) {
            $controllers = $this->getControllers();
            if ($controllers->has($method) && ! $override) {
                if ($throw_error) {
                    throw new RuntimeException('Controller for '.$method.' is already set.');
                }

                return false;
            }

            // Proceed with setting

            $value = null;

            if ($this->isInvokable($controller)) {
                $value = $controller;
            } elseif ($this->mustHaveMethod($method, $controller)) {
                $value = [$controller, $method];
            }

            if ($value) {
                $controllers->put($method, $value);
                $this->controllers = $controllers->toArray();
                $this->getControllers(rehydrate: true);

                return true;
            }

            if ($throw_error) {
                throw new RuntimeException($controller.' must either be invokable or has '.$method.' method.');
            }

            return false;
        }

        return false;
    }

    /**
     * @param  string  $registerController
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    public function setRegisterController(string $registerController, bool $override = false, bool $throw_error = true): bool
    {
        return $this->setController('register', $registerController, $override, $throw_error);
    }

    /**
     * @param  string  $logoutControllerClass
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    public function setLogoutController(string $logoutControllerClass, bool $override = false, bool $throw_error = true): bool
    {
        return $this->setController('logout', $logoutControllerClass, $override, $throw_error);
    }

    /**
     * @param  string  $meController
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    public function setMeController(string $meController, bool $override = false, bool $throw_error = true): bool
    {
        return $this->setController('me', $meController, $override, $throw_error);
    }
}
