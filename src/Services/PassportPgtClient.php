<?php

namespace Fld3\PassportPgtClient\Services;

use Fld3\PassportPgtClient\Http\Controllers\DefaultAuthController;
use Fld3\PassportPgtClient\Traits\HasAuthMethodsTrait;
use Fligno\ApiSdkKit\Abstracts\BaseApiSdkService;
use Fligno\ApiSdkKit\Services\MakeRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * Class PassportPgtClient
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class PassportPgtClient extends BaseApiSdkService
{
    use HasAuthMethodsTrait;

    /**
     * @var array
     */
    protected array $controllers = [];

    /**
     * @param  string|null  $auth_controller
     */
    public function __construct(string $auth_controller = null)
    {
        // Rehydrate first
        $this->controllers = $this->getControllers()->toArray();

        $this->setAuthController($auth_controller ?? DefaultAuthController::class, false, false);
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

    /***** BASE API SERVICE METHODS  *****/

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->getPassportUrl();
    }

    /**
     * Since tokens are very sensitive information, they should not be logged.
     *
     * @return MakeRequest
     */
    public function getMakeRequest(): MakeRequest
    {
        return parent::getMakeRequest()->returnAsResponse();
    }

    /***** CONTROLLER-RELATED *****/

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
     * @param  string  $login_controller
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    public function setLoginController(string $login_controller, bool $override = false, bool $throw_error = true): bool
    {
        return $this->setController('login', $login_controller, $override, $throw_error);
    }

    /**
     * @param  string  $refresh_token_controller
     * @param  bool  $override
     * @param  bool  $throw_error
     * @return bool
     */
    public function setRefreshTokenController(string $refresh_token_controller, bool $override = false, bool $throw_error = true): bool
    {
        return $this->setController('refreshToken', $refresh_token_controller, $override, $throw_error);
    }

    /***** AUTH-RELATED FUNCTIONS *****/

    /**
     * @param  array  $data
     * @return MakeRequest
     */
    public function register(array $data): MakeRequest
    {
        return tap($this->getMakeRequest()->asJson()->data($data))->post('api/oauth/register');
    }

    /**
     * @param  string  $username
     * @param  string  $password
     * @param  array  $scopes
     * @return MakeRequest
     */
    public function login(string $username, string $password, array $scopes = []): MakeRequest
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->getPasswordGrantClientId(),
            'client_secret' => $this->getPasswordGrantClientSecret(),
            'username' => $username,
            'password' => $password,
            'scope' => $this->getSluggedScopes($scopes),
        ];

        return tap($this->getMakeRequest()->asJson()->data($data))->post('oauth/token');
    }

    /**
     * @param  string  $refresh_token
     * @param  array  $scopes
     * @return MakeRequest
     */
    public function refreshToken(string $refresh_token, array $scopes = []): MakeRequest
    {
        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id' => $this->getPasswordGrantClientId(),
            'client_secret' => $this->getPasswordGrantClientSecret(),
            'scope' => $this->getSluggedScopes($scopes),
        ];

        return tap($this->getMakeRequest()->asJson()->data($data))->post('oauth/token');
    }

    /**
     * @param  string|null  $token
     * @return MakeRequest
     */
    public function logout(string|null $token): MakeRequest
    {
        $headers = [
            'Authorization' => 'Bearer '.$token,
        ];

        return tap($this->getMakeRequest()->asJson()->headers($headers))->post('api/oauth/logout');
    }

    /**
     * @param  string|null  $token
     * @return MakeRequest
     */
    public function getSelf(string|null $token): MakeRequest
    {
        $headers = [
            'Authorization' => 'Bearer '.$token,
        ];

        return tap($this->getMakeRequest()->asJson()->headers($headers))->get('api/oauth/me');
    }

    /**
     * @param  array  $scopes
     * @return string
     */
    protected function getSluggedScopes(array $scopes = []): string
    {
        return collect($scopes)->map(fn (string $scope) => Str::slug($scope))->filter()->implode(' ');
    }
}
