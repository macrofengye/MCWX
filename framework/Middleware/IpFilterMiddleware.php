<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-10-26
 * Time: 上午11:44
 */

namespace Polymer\Middleware;

use Polymer\Utils\Constants;
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class IpFilterMiddleware
{
    protected $addresses = [];
    protected $mode = null;
    protected $allowed = null;
    protected $handler = null;

    public function __construct($addresses = [], $mode = Constants::ALLOW)
    {
        foreach ($addresses as $address) {
            if (is_array($address)) {
                $this->addIpRange($address[0], $address[1]);
            } else {
                $this->addIp($address);
            }
        }
        $this->patterns = $addresses;
        $this->mode = $mode;
        $this->handler = function (Request $request, Response $response) {
            $response = $response->withStatus(401);
            $response->getBody()->write("Access denied");
            return $response;
        };
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        if ($this->mode == Constants::ALLOW)
            $this->allowed = $this->allow($request);
        if ($this->mode == Constants::DENY)
            $this->allowed = $this->deny($request);
        if (!$this->allowed) {
            $handler = $this->handler;
            return $handler($request, $response);
        }
        $response = $next($request, $response);
        return $response;
    }

    public function allow(Request $request)
    {
        $clientAddress = ip2long($_SERVER["REMOTE_ADDR"]);
        if (in_array($clientAddress, $this->addresses))
            return false;
        return true;
    }

    public function deny(Request $request)
    {
        $clientAddress = ip2long($_SERVER["REMOTE_ADDR"]);
        if (in_array($clientAddress, $this->addresses))
            return true;
        return false;
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function addIpRange($start, $end)
    {
        foreach (range(ip2long($start), ip2long($end)) as $address)
            $this->addresses[] = $address;
        return $this;
    }

    public function addIp($ip)
    {
        $this->addresses[] = ip2long($ip);
        return $this;
    }
}