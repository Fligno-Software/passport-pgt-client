<?php

namespace Fld3\PassportPgtClient;

use Fld3\PassportPgtClient\Abstracts\BaseAuthClientController;
use Fld3\PassportPgtClient\Http\Controllers\AuthClientController;
use Fld3\PassportPgtClient\Traits\HasAuthClientGetSelfTrait;
use Fld3\PassportPgtClient\Traits\HasAuthClientLoginTrait;
use Fld3\PassportPgtClient\Traits\HasAuthClientLogoutTrait;
use Fld3\PassportPgtClient\Traits\HasAuthClientRefreshTokenTrait;
use Fligno\ApiSdkKit\Models\AuditLog;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\Response;
use Illuminate\Routing\Controller;
use RuntimeException;
use Throwable;

/**
 * Class PassportPgtClient
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class PassportPgtClient
{
    /**
     * @var string
     */
    protected string $authClientController;

    /**
     * @var array
     */
    protected array $loginAuthController = [];

    /**
     * @var array
     */
    protected array $logoutAuthController = [];

    /**
     * @var array
     */
    protected array $meAuthController = [];

    /**
     * @var array
     */
    protected array $refreshTokenAuthController = [];

    /**
     * @param string|null $authClientControllerClass
     */
    public function __construct(string $authClientControllerClass = null)
    {
        if ($authClientControllerClass) {
            try {
                $this->setAuthClientController($authClientControllerClass);
            } catch (Throwable) {
            }
        }

        if (! isset($this->authClientController)) {
            $this->authClientController = AuthClientController::class;
        }
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
     * @param string $authServerControllerClass
     */
    public function setAuthClientController(string $authServerControllerClass): void
    {
        if (is_subclass_of($authServerControllerClass, BaseAuthClientController::class)) {
            $this->authClientController = $authServerControllerClass;
        } else {
            throw new RuntimeException('Controller class does not extend BaseAuthServerController class.');
        }
    }

    /**
     * @return string|BaseAuthClientController
     */
    public function getAuthClientController(): string|BaseAuthClientController
    {
        return $this->authClientController;
    }

    /**
     * @param string $loginAuthControllerClass
     * @return void
     */
    public function setLoginAuthController(string $loginAuthControllerClass): void
    {
        if (is_subclass_of($loginAuthControllerClass, Controller::class)) {
            if (class_uses_trait($loginAuthControllerClass, HasAuthClientLoginTrait::class)) {
                $this->loginAuthController = [$loginAuthControllerClass, 'login'];
            } else {
                $this->loginAuthController = [$loginAuthControllerClass];
            }
        }
    }

    /**
     * @return array
     */
    public function getLoginAuthController(): array
    {
        if (count($this->loginAuthController)) {
            return $this->loginAuthController;
        }

        return [$this->authClientController, 'login'];
    }

    /**
     * @param string $logoutAuthControllerClass
     * @return void
     */
    public function setLogoutAuthController(string $logoutAuthControllerClass): void
    {
        if (is_subclass_of($logoutAuthControllerClass, Controller::class)) {
            if (class_uses_trait($logoutAuthControllerClass, HasAuthClientLogoutTrait::class)) {
                $this->logoutAuthController = [$logoutAuthControllerClass, 'logout'];
            } else {
                $this->logoutAuthController = [$logoutAuthControllerClass];
            }
        }
    }

    /**
     * @return array
     */
    public function getLogoutAuthController(): array
    {
        if (count($this->logoutAuthController)) {
            return $this->logoutAuthController;
        }

        return [$this->authClientController, 'logout'];
    }

    /**
     * @param array $meAuthController
     * @return void
     */
    public function setMeAuthController(array $meAuthController): void
    {
        if (is_subclass_of($meAuthController, Controller::class)) {
            if (class_uses_trait($meAuthController, HasAuthClientGetSelfTrait::class)) {
                $this->meAuthController = [$meAuthController, 'me'];
            } else {
                $this->meAuthController = [$meAuthController];
            }
        }
    }

    /**
     * @return array
     */
    public function getMeAuthController(): array
    {
        if (count($this->meAuthController)) {
            return $this->meAuthController;
        }

        return [$this->authClientController, 'me'];
    }

    /**
     * @param array $refreshTokenAuthController
     * @return void
     */
    public function setRefreshTokenAuthController(array $refreshTokenAuthController): void
    {
        if (is_subclass_of($refreshTokenAuthController, Controller::class)) {
            if (class_uses_trait($refreshTokenAuthController, HasAuthClientRefreshTokenTrait::class)) {
                $this->refreshTokenAuthController = [$refreshTokenAuthController, 'refreshToken'];
            } else {
                $this->refreshTokenAuthController = [$refreshTokenAuthController];
            }
        }
    }

    /**
     * @return array
     */
    public function getRefreshTokenAuthController(): array
    {
        if (count($this->refreshTokenAuthController)) {
            return $this->refreshTokenAuthController;
        }

        return [$this->authClientController, 'refreshToken'];
    }

    /***** AUTH-RELATED FUNCTIONS *****/

    /**
     * @param string $username
     * @param string $password
     * @return AuditLog|PromiseInterface|Builder|Response
     */
    public function login(string $username, string $password): AuditLog|Builder|PromiseInterface|Response
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
     * @param string $refresh_token
     * @return AuditLog|PromiseInterface|Builder|Response
     */
    public function refreshToken(string $refresh_token): AuditLog|Builder|PromiseInterface|Response
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
     * @param string|null $token
     * @return AuditLog|PromiseInterface|Builder|Response
     */
    public function logout(string|null $token): AuditLog|Builder|PromiseInterface|Response
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        return makeRequest($this->getPassportUrl())->headers($headers)->executePost('api/oauth/logout');
    }

    /**
     * @param string|null $token
     * @return AuditLog|PromiseInterface|Builder|Response
     */
    public function getSelf(string|null $token): AuditLog|Builder|PromiseInterface|Response
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        return makeRequest($this->getPassportUrl())->headers($headers)->executeGet('api/oauth/me');
    }
}
