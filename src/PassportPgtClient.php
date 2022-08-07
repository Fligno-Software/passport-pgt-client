<?php

namespace Fld3\PassportPgtClient;

use Fld3\PassportPgtClient\Http\Controllers\DefaultAuthController;
use Fld3\PassportPgtClient\Traits\HasAuthMethodsTrait;
use Fligno\ApiSdkKit\Models\AuditLog;
use Fligno\StarterKit\Traits\HasTaggableCacheTrait;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class PassportPgtClient
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class PassportPgtClient
{
    use HasAuthMethodsTrait, HasTaggableCacheTrait;

    /**
     * @var array
     */
    protected array $controllers = [];

    /**
     * @param  string|null  $authController
     */
    public function __construct(string $authController = null)
    {
        // Rehydrate first
        $this->controllers = $this->getControllers()->toArray();

        $this->setAuthController($authController ?? DefaultAuthController::class, false, false);
    }

    /**
     * @return string
     */
    public function getMainTag(): string
    {
        return 'passport-pgt-client';
    }

    /***** CONFIG-RELATED *****/

    /**
     * @return string
     */
    public function getPassportUrl(): string
    {
        return config('passport-pgt-client.passport_url');
    }

    /**
     * @return string|int|null
     */
    public function getPasswordGrantClientId(): int|string|null
    {
        return config('passport-pgt-client.password_grant_client.id');
    }

    /**
     * @return string|null
     */
    public function getPasswordGrantClientSecret(): ?string
    {
        return config('passport-pgt-client.password_grant_client.secret');
    }

    /***** CONTROLLER-RELATED *****/

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
     * @param  string  $controller
     * @param  bool  $override
     * @param  bool  $throw_error
     */
    public function setAuthController(string $controller, bool $override = false, bool $throw_error = true): void
    {
        if (is_subclass_of($controller, Controller::class)) {
            $this->setRegisterController($controller, $override, $throw_error);
            $this->setLoginController($controller, $override, $throw_error);
            $this->setLogoutController($controller, $override, $throw_error);
            $this->setMeController($controller, $override, $throw_error);
            $this->setRefreshTokenController($controller, $override, $throw_error);
        }
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
     * @param  string  $loginController
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    public function setLoginController(string $loginController, bool $override = false, bool $throw_error = true): bool
    {
        return $this->setController('login', $loginController, $override, $throw_error);
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

    /**
     * @param  string  $refreshTokenController
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    public function setRefreshTokenController(string $refreshTokenController, bool $override = false, bool $throw_error = true): bool
    {
        return $this->setController('refreshToken', $refreshTokenController, $override, $throw_error);
    }

    /***** AUTH-RELATED FUNCTIONS *****/

    /**
     * @param  array  $data
     * @return AuditLog|PromiseInterface|Builder|Response|\Illuminate\Http\Response|null
     */
    public function register(array $data): \Illuminate\Http\Response|AuditLog|Builder|PromiseInterface|Response|null
    {
        return makeRequest($this->getPassportUrl())->data($data)->executePost('api/oauth/register');
    }

    /**
     * @param  string  $username
     * @param  string  $password
     * @return AuditLog|PromiseInterface|Builder|Response|\Illuminate\Http\Response|null
     */
    public function login(string $username, string $password): \Illuminate\Http\Response|AuditLog|Builder|PromiseInterface|Response|null
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->getPasswordGrantClientId(),
            'client_secret' => $this->getPasswordGrantClientSecret(),
            'username' => $username,
            'password' => $password,
            'scope' => '',
        ];

        return makeRequest($this->getPassportUrl())->data($data)->executePost('oauth/token');
    }

    /**
     * @param  string  $refresh_token
     * @return AuditLog|PromiseInterface|Builder|Response|\Illuminate\Http\Response|null
     */
    public function refreshToken(string $refresh_token): \Illuminate\Http\Response|AuditLog|Builder|PromiseInterface|Response|null
    {
        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id' => $this->getPasswordGrantClientId(),
            'client_secret' => $this->getPasswordGrantClientSecret(),
            'scope' => '',
        ];

        return makeRequest($this->getPassportUrl())->data($data)->executePost('oauth/token');
    }

    /**
     * @param  string|null  $token
     * @return AuditLog|PromiseInterface|Builder|Response|\Illuminate\Http\Response|null
     */
    public function logout(string|null $token): \Illuminate\Http\Response|AuditLog|Builder|PromiseInterface|Response|null
    {
        $headers = [
            'Authorization' => 'Bearer '.$token,
        ];

        return makeRequest($this->getPassportUrl())->headers($headers)->executePost('api/oauth/logout');
    }

    /**
     * @param  string|null  $token
     * @return AuditLog|PromiseInterface|Builder|Response|\Illuminate\Http\Response|null
     */
    public function getSelf(string|null $token): \Illuminate\Http\Response|AuditLog|Builder|PromiseInterface|Response|null
    {
        $headers = [
            'Authorization' => 'Bearer '.$token,
        ];

        return makeRequest($this->getPassportUrl())->headers($headers)->executeGet('api/oauth/me');
    }
}
