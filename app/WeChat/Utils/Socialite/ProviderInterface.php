<?php
namespace WeChat\Utils\Socialite;

interface ProviderInterface
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();

    /**
     * Get the User instance for the authenticated user.
     *
     * @param \Overtrue\Socialite\AccessTokenInterface $token
     *
     * @return \Overtrue\Socialite\User
     */
    public function user(AccessTokenInterface $token = null);
}
