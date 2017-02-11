<?php

namespace MComponent\WX\SWA\WeChat\OpenPlatform;

use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;
use MComponent\WX\SWA\WeChat\OpenPlatform\Traits\VerifyTicketTrait;
use MComponent\WX\SWA\WeChat\Server\Guard as ServerGuard;
use MComponent\WX\SWA\WeChat\Support\Arr;
use Symfony\Component\HttpFoundation\Request;

class Guard extends ServerGuard
{
    use VerifyTicketTrait;

    /**
     * Wechat push event types.
     *
     * @var array
     */
    protected $eventTypeMappings = [
        'authorized' => EventHandlers\Authorized::class,
        'unauthorized' => EventHandlers\Unauthorized::class,
        'updateauthorized' => EventHandlers\UpdateAuthorized::class,
        'component_verify_ticket' => EventHandlers\ComponentVerifyTicket::class,
    ];

    /**
     * Guard constructor.
     *
     * @param string       $token
     * @param VerifyTicket $ticketTrait
     * @param Request|null $request
     */
    public function __construct($token, VerifyTicket $ticketTrait, Request $request = null)
    {
        parent::__construct($token, $request);

        $this->setVerifyTicket($ticketTrait);
    }

    /**
     * Return for laravel-wechat.
     *
     * @return array
     */
    public function listServe()
    {
        $class = $this->getHandleClass();

        $message = $this->getCollectedMessage();

        call_user_func([new $class($this->verifyTicket), 'handle'], $message);

        return [
            $message->get('InfoType'), $message,
        ];
    }

    /**
     * Listen for wechat push event.
     *
     * @param callable|null $callback
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function listen($callback = null)
    {
        $message = $this->getCollectedMessage();

        $class = $this->getHandleClass();

        if (is_callable($callback)) {
            $callback(
                call_user_func([new $class($this->verifyTicket), 'forward'], $message)
            );
        }

        return call_user_func([new $class($this->verifyTicket), 'handle'], $message);
    }

    /**
     * Get handler class.
     *
     * @return \MComponent\WX\SWA\WeChat\OpenPlatform\EventHandlers\EventHandler
     *
     * @throws InvalidArgumentException
     */
    private function getHandleClass()
    {
        $message = $this->getCollectedMessage();

        $type = $message->get('InfoType');

        if (!$class = Arr::get($this->eventTypeMappings, $type)) {
            throw new InvalidArgumentException("Event Info Type \"$type\" does not exists.");
        }

        return $class;
    }
}
