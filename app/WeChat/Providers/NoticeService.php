<?php
namespace WeChat\Providers;

use WeChat\Utils\WeChat\Notice\Notice;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class NoticeService.
 */
class NoticeService implements ServiceProviderInterface
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
        $pimple['notice'] = function ($pimple) {
            return new Notice($pimple['access_token']);
        };
    }
}
