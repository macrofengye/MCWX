<?php
namespace MComponent\WX\SWA\WeChat\Payment\MerchantPay;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;
use MComponent\WX\SWA\WeChat\Payment\Merchant;
use MComponent\WX\SWA\WeChat\Support\Collection;
use MComponent\WX\SWA\WeChat\Support\XML;
use Psr\Http\Message\ResponseInterface;

/**
 * Class API.
 */
class API extends AbstractAPI
{
    /**
     * Merchant instance.
     *
     * @var Merchant
     */
    protected $merchant;

    // api
    const API_SEND = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    const API_QUERY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';

    /**
     * API constructor.
     *
     * @param \MComponent\WX\SWA\WeChat\Payment\Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Query MerchantPay.
     *
     * @param string $mchBillNo
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     *
     * @notice mch_id when query, but mchid when send
     */
    public function query($mchBillNo)
    {
        $params = [
            'appid' => $this->merchant->app_id,
            'mch_id' => $this->merchant->merchant_id,
            'partner_trade_no' => $mchBillNo,
        ];

        return $this->request(self::API_QUERY, $params);
    }

    /**
     * Send MerchantPay.
     *
     * @param array $params
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function send(array $params)
    {
        $params['mchid'] = $this->merchant->merchant_id;
        $params['mch_appid'] = $this->merchant->app_id;

        return $this->request(self::API_SEND, $params);
    }

    /**
     * Merchant setter.
     *
     * @param Merchant $merchant
     *
     * @return $this
     */
    public function setMerchant(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Merchant getter.
     *
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Make a API request.
     *
     * @param string $api
     * @param array $params
     * @param string $method
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    protected function request($api, array $params, $method = 'post')
    {
        $params = array_filter($params);
        $params['nonce_str'] = uniqid();
        $params['sign'] = \MComponent\WX\SWA\WeChat\Payment\generate_sign($params, $this->merchant->key, 'md5');

        $options = [
            'body' => XML::build($params),
            'cert' => $this->merchant->get('cert_path'),
            'ssl_key' => $this->merchant->get('key_path'),
        ];

        return $this->parseResponse($this->getHttp()->request($api, $method, $options));
    }

    /**
     * Parse Response XML to array.
     *
     * @param ResponseInterface $response
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    protected function parseResponse($response)
    {
        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array)XML::parse($response));
    }
}
