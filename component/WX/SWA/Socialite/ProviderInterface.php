<?php
namespace MComponent\WX\SWA\Socialite;

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
     * @param \MComponent\WX\SWA\Socialite\AccessTokenInterface $token
     *
     * @return \MComponent\WX\SWA\Socialite\User
     */
    public function user(AccessTokenInterface $token = null);
}
