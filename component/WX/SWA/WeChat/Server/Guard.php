<?php
namespace MComponent\WX\SWA\WeChat\Server;

use MComponent\WX\SWA\WeChat\Core\Exceptions\FaultException;
use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;
use MComponent\WX\SWA\WeChat\Core\Exceptions\RuntimeException;
use MComponent\WX\SWA\WeChat\Encryption\Encryptor;
use MComponent\WX\SWA\WeChat\Message\AbstractMessage;
use MComponent\WX\SWA\WeChat\Message\Raw as RawMessage;
use MComponent\WX\SWA\WeChat\Message\Text;
use MComponent\WX\SWA\WeChat\Support\Collection;
use MComponent\WX\SWA\WeChat\Support\Log;
use MComponent\WX\SWA\WeChat\Support\XML;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Guard.
 */
class Guard
{
    /**
     * Empty string.
     */
    const SUCCESS_EMPTY_RESPONSE = 'success';

    const TEXT_MSG = 2;
    const IMAGE_MSG = 4;
    const VOICE_MSG = 8;
    const VIDEO_MSG = 16;
    const SHORT_VIDEO_MSG = 32;
    const LOCATION_MSG = 64;
    const LINK_MSG = 128;
    const DEVICE_EVENT_MSG = 256;
    const DEVICE_TEXT_MSG = 512;
    const EVENT_MSG = 1048576;
    const ALL_MSG = 1049598;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var string|callable
     */
    protected $messageHandler;

    /**
     * @var int
     */
    protected $messageFilter;

    /**
     * @var array
     */
    protected $messageTypeMapping = [
        'text' => 2,
        'image' => 4,
        'voice' => 8,
        'video' => 16,
        'shortvideo' => 32,
        'location' => 64,
        'link' => 128,
        'device_event' => 256,
        'device_text' => 512,
        'event' => 1048576,
    ];

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Constructor.
     *
     * @param string $token
     * @param Request $request
     */
    public function __construct($token, Request $request = null, Response $response = null)
    {
        $this->token = $token;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Enable/Disable debug mode.
     *
     * @param bool $debug
     *
     * @return $this
     */
    public function debug($debug = true)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Handle and return response.
     *
     * @return Response
     *
     * @throws BadRequestException
     */
    public function serve()
    {
        Log::debug('Request received:', [
            'Method' => $this->request->getMethod(),
            'URI' => $this->request->getUri(),
            'Query' => $this->request->getQueryParams(),
            'Protocal' => $this->request->getProtocolVersion(),
            'Content' => $this->request->getBody(),
        ]);

        $this->validate($this->token);

        if ($str = $this->request->getParam('echostr')) {
            Log::debug("Output 'echostr' is '$str'.");

            return $this->response->write($str);
        }

        $result = $this->handleRequest();

        $response = $this->buildResponse($result['to'], $result['from'], $result['msg']);

        Log::debug('Server response created:', compact('response'));

        $this->response->getBody()->write($response);

        return $this->response;
    }

    /**
     * Validation request params.
     *
     * @param string $token
     *
     * @throws FaultException
     */
    public function validate($token)
    {
        $params = [
            $token,
            $this->request->getParam('timestamp'),
            $this->request->getParam('nonce'),
        ];

        if (!$this->debug && $this->request->getParam('signature') !== $this->signature($params)) {
            throw new FaultException('Invalid request signature.', 400);
        }
    }

    /**
     * Add a event listener.
     *
     * @param callable $callback
     * @param int $option
     *
     * @return Guard
     *
     * @throws InvalidArgumentException
     */
    public function setMessageHandler($callback = null, $option = self::ALL_MSG)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Argument #2 is not callable.');
        }

        $this->messageHandler = $callback;
        $this->messageFilter = $option;

        return $this;
    }

    /**
     * Return the message listener.
     *
     * @return string
     */
    public function getMessageHandler()
    {
        return $this->messageHandler;
    }

    /**
     * Request getter.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Request setter.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }


    /**
     * Set Encryptor.
     *
     * @param Encryptor $encryptor
     *
     * @return Guard
     */
    public function setEncryptor(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;

        return $this;
    }

    /**
     * Return the encryptor instance.
     *
     * @return Encryptor
     */
    public function getEncryptor()
    {
        return $this->encryptor;
    }

    /**
     * Build response.
     *
     * @param $to
     * @param $from
     * @param mixed $message
     *
     * @return string
     *
     * @throws mixed
     */
    protected function buildResponse($to, $from, $message)
    {
        if (empty($message) || $message === self::SUCCESS_EMPTY_RESPONSE) {
            return self::SUCCESS_EMPTY_RESPONSE;
        }

        if ($message instanceof RawMessage) {
            return $message->get('content', self::SUCCESS_EMPTY_RESPONSE);
        }

        if (is_string($message) || is_numeric($message)) {
            $message = new Text(['content' => $message]);
        }

        if (!$this->isMessage($message)) {
            $messageType = gettype($message);
            throw new InvalidArgumentException("Invalid Message type .'{$messageType}'");
        }

        $response = $this->buildReply($to, $from, $message);

        if ($this->isSafeMode()) {
            Log::debug('Message safe mode is enable.');
            $response = $this->encryptor->encryptMsg(
                $response,
                $this->request->getParam('nonce'),
                $this->request->getParam('timestamp')
            );
        }

        return $response;
    }

    /**
     * Whether response is message.
     *
     * @param mixed $message
     *
     * @return bool
     */
    protected function isMessage($message)
    {
        if (is_array($message)) {
            foreach ($message as $element) {
                if (!is_subclass_of($element, AbstractMessage::class)) {
                    return false;
                }
            }

            return true;
        }

        return is_subclass_of($message, AbstractMessage::class);
    }

    /**
     * Get request message.
     *
     * @return array
     * @throws BadRequestException
     */
    public function getMessage()
    {
        $message = $this->parseMessageFromRequest($this->request->getBody());

        if (!is_array($message) || empty($message)) {
            throw new BadRequestException('Invalid request.');
        }

        return $message;
    }

    /**
     * Handle request.
     *
     * @return array
     *
     * @throws \MComponent\WX\SWA\WeChat\Core\Exceptions\RuntimeException
     * @throws \MComponent\WX\SWA\WeChat\Server\BadRequestException
     */
    protected function handleRequest()
    {
        $message = $this->getMessage();
        $msg = $this->handleMessage($message);

        return [
            'to' => $message['FromUserName'],
            'from' => $message['ToUserName'],
            'msg' => $msg,
        ];
    }

    /**
     * Handle message.
     *
     * @param array $message
     *
     * @return mixed
     */
    protected function handleMessage($message)
    {
        $handler = $this->messageHandler;

        if (!is_callable($handler)) {
            Log::debug('No handler enabled.');

            return;
        }

        Log::debug('Message detail:', $message);

        $message = new Collection($message);

        $type = $this->messageTypeMapping[$message->get('MsgType')];

        $response = null;

        if ($this->messageFilter & $type) {
            $response = call_user_func_array($handler, [$message]);
        }

        return $response;
    }

    /**
     * Build reply XML.
     *
     * @param string $to
     * @param string $from
     * @param AbstractMessage $message
     *
     * @return string
     */
    protected function buildReply($to, $from, $message)
    {
        $base = [
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => is_array($message) ? current($message)->getType() : $message->getType(),
        ];

        $transformer = new Transformer();

        return XML::build(array_merge($base, $transformer->transform($message)));
    }

    /**
     * Get signature.
     *
     * @param array $request
     *
     * @return string
     */
    protected function signature($request)
    {
        sort($request, SORT_STRING);

        return sha1(implode($request));
    }

    /**
     * Parse message array from raw php input.
     *
     * @param string|resource $content
     *
     * @throws \MComponent\WX\SWA\WeChat\Core\Exceptions\RuntimeException
     * @throws \MComponent\WX\SWA\WeChat\Encryption\EncryptionException
     *
     * @return array
     */
    protected function parseMessageFromRequest($content)
    {
        $content = strval($content);

        if ($this->isSafeMode()) {
            if (!$this->encryptor) {
                throw new RuntimeException('Safe mode Encryptor is necessary, please use Guard::setEncryptor(Encryptor $encryptor) set the encryptor instance.');
            }

            $message = $this->encryptor->decryptMsg(
                $this->request->getParam('msg_signature'),
                $this->request->getParam('nonce'),
                $this->request->getParam('timestamp'),
                $content
            );
        } else {
            $message = XML::parse($content);
        }

        return $message;
    }

    /**
     * Check the request message safe mode.
     *
     * @return bool
     */
    private function isSafeMode()
    {
        return $this->request->getParam('encrypt_type') && $this->request->getParam('encrypt_type') === 'aes';
    }
}
