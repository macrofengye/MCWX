<?php
namespace WeChat\Utils\Socialite;

use Slim\Http\Response;

interface ProviderInterface
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return Response
     */
    public function redirect();

    /**
     * Get the User instance for the authenticated user.
     *
     * @param \WeChat\Utils\Socialite\AccessTokenInterface $token
     *
     * @return \WeChat\Utils\Socialite\User
     */
    public function user(AccessTokenInterface $token = null);
}
