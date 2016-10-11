<?php
namespace WeChat\Utils\WeChat\Payment;

use WeChat\Utils\WeChat\Core\Exceptions\FaultException;
use WeChat\Utils\WeChat\Support\Collection;
use WeChat\Utils\WeChat\Support\XML;

/**
 * Class Notify.
 */
class Notify
{
    /**
     * Merchant instance.
     *
     * @var \WeChat\Utils\WeChat\Payment\Merchant
     */
    protected $merchant;

    /**
     * Request instance.
     *
     * @var \Slim\Http\Request
     */
    protected $request;

    /**
     * Payment notify (extract from XML).
     *
     * @var Collection
     */
    protected $notify;

    /**
     * Constructor.
     *
     * @param Merchant $merchant
     * @param Request $request
     */
    public function __construct(Merchant $merchant, Request $request = null)
    {
        $this->merchant = $merchant;
        $this->request = $request;
    }

    /**
     * Validate the request params.
     *
     * @return bool
     */
    public function isValid()
    {
        $localSign = generate_sign($this->getNotify()->except('sign')->all(), $this->merchant->key, 'md5');

        return $localSign === $this->getNotify()->get('sign');
    }

    /**
     * Return the notify body from request.
     *
     * @return \WeChat\Utils\WeChat\Support\Collection
     *
     * @throws \WeChat\Utils\WeChat\Core\Exceptions\FaultException
     */
    public function getNotify()
    {
        if (!empty($this->notify)) {
            return $this->notify;
        }
        try {
            $xml = XML::parse(strval($this->request->getContent()));
        } catch (\Throwable $t) {
            throw new FaultException('Invalid request XML: ' . $t->getMessage(), 400);
        } catch (\Exception $e) {
            throw new FaultException('Invalid request XML: ' . $e->getMessage(), 400);
        }

        if (!is_array($xml) || empty($xml)) {
            throw new FaultException('Invalid request XML.', 400);
        }

        return $this->notify = new Collection($xml);
    }
}
