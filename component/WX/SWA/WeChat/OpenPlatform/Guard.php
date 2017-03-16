<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform;

use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;
use MComponent\WX\SWA\WeChat\Server\Guard as ServerGuard;
use MComponent\WX\SWA\WeChat\Support\Collection;
use Pimple\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Guard extends ServerGuard
{
    const EVENT_AUTHORIZED = 'authorized';
    const EVENT_UNAUTHORIZED = 'unauthorized';
    const EVENT_UPDATE_AUTHORIZED = 'updateauthorized';
    const EVENT_COMPONENT_VERIFY_TICKET = 'component_verify_ticket';

    /**
     * Container in the scope of the open platform authorization.
     *
     * @var Container
     */
    protected $container;

    /**
     * Guard constructor.
     *
     * @param string $token
     * @param Request $request
     */
    public function __construct($token, Request $request = null)
    {
        parent::__construct($token, $request);
    }

    /**
     * Sets the container for use of event handlers.
     *
     * @param Container $container
     *
     * @see getDefaultHandler()
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function serve()
    {
        // If sees the `auth_code` query parameter in the url, that is,
        // authorization is successful and it calls back, meanwhile, an
        // ` authorized` event, which also includes the auth code, is sent
        // from WeChat, and that event will be handled.
        if ($this->request->getParam('auth_code')) {
            return $this->response->write('success');
        }
        $this->handleMessage($this->getMessage());
        return $this->response->write('success');
    }

    /**
     * Return for laravel-wechat.
     *
     * @return array
     * @throws \Exception
     */
    public function listServe()
    {
        $message = $this->getMessage();
        $this->handleMessage($message);
        $message = new Collection($message);
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
     * @throws InvalidArgumentException | \Exception
     */
    public function listen($callback = null)
    {
        try {
            if ($callback) {
                $this->setMessageHandler($callback);
            }
            return $this->serve();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function handleMessage($message)
    {
        try {
            if (is_array($message)) {
                $message = new Collection($message);
            }
            $handler = $this->getDefaultHandler($message->get('InfoType'));
            $result = $handler->handle($message);
            if (is_array($result) || $result instanceof Collection) {
                $message->merge($result);
            } else {
                if (!empty($result)) {
                    $message->set('result', $result);
                }
            }
            if ($customHandler = $this->getMessageHandler()) {
                $customHandler($message);
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Gets the default handler by the info type.
     *
     * @param $type
     *
     * @return EventHandlers\EventHandler
     * @throws InvalidArgumentException | \Exception
     */
    protected function getDefaultHandler($type)
    {
        try {
            $handler = $this->container->offsetGet("open_platform.handlers.{$type}");
            if (!$handler) {
                throw new InvalidArgumentException("EventHandler \"$type\" does not exists.");
            }
            return $handler;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
