<?php

namespace WeChat\Providers;

use WeChat\Utils\WeChat\Payment\LuckyMoney\LuckyMoney;
use WeChat\Utils\WeChat\Payment\Merchant;
use WeChat\Utils\WeChat\Payment\MerchantPay\MerchantPay;
use WeChat\Utils\WeChat\Payment\Payment;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class PaymentProvider.
 */
class PaymentProvider implements ServiceProviderInterface
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
        $pimple['merchant'] = function ($pimple) {
            $config = array_merge(
                ['app_id' => $pimple['config']['app_id']],
                $pimple['config']->get('payment', [])
            );

            return new Merchant($config);
        };

        $pimple['payment'] = function ($pimple) {
            return new Payment($pimple['merchant']);
        };

        $pimple['lucky_money'] = function ($pimple) {
            return new LuckyMoney($pimple['merchant']);
        };

        $pimple['merchant_pay'] = function ($pimple) {
            return new MerchantPay($pimple['merchant']);
        };
    }
}
