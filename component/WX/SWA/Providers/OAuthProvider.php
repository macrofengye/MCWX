<?php
namespace MComponent\WX\SWA\Providers;

use MComponent\WX\SWA\Socialite\SocialiteManager as Socialite;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class OAuthProvider.
 */
class OAuthProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['oAuth'] = function ($pimple) {
            $callback = $this->prepareCallbackUrl($pimple);
            $scopes = app()->config('wechat.oauth.scopes');
            $socialite = (
            new Socialite(
                [
                    'wechat' => [
                        'open_platform' => app()->config('wechat.open_platform'),
                        'client_id' => app()->config('wechat.app_id'),
                        'client_secret' => app()->config('wechat.secret'),
                        'redirect' => $callback,
                    ],
                ], $pimple['request']
            )
            )->driver('wechat');

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite;
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $pimple
     *
     * @return string
     */
    private function prepareCallbackUrl($pimple)
    {
        $callback = app()->config('wechat.oauth.callback');
        if (0 === stripos($callback, 'http')) {
            return $callback;
        }
        $baseUrl = $pimple['request']->getUri()->getScheme() . '://' . $pimple['request']->getUri()->getHost();
        echo $baseUrl;

        return $baseUrl . '/' . ltrim($callback, '/');
    }
}
