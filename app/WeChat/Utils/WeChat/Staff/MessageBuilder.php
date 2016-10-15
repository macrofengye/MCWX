<?php
namespace WeChat\Utils\WeChat\Staff;

use WeChat\Utils\WeChat\Core\Exceptions\InvalidArgumentException;
use WeChat\Utils\WeChat\Core\Exceptions\RuntimeException;
use WeChat\Utils\WeChat\Message\AbstractMessage;
use WeChat\Utils\WeChat\Message\Raw as RawMessage;
use WeChat\Utils\WeChat\Message\Text;

/**
 * Class MessageBuilder.
 */
class MessageBuilder
{
    /**
     * Message to send.
     *
     * @var \WeChat\Utils\WeChat\Message\AbstractMessage;
     */
    protected $message;

    /**
     * Message target user open id.
     *
     * @var string
     */
    protected $to;

    /**
     * Message sender staff id.
     *
     * @var string
     */
    protected $account;

    /**
     * Staff instance.
     *
     * @var \WeChat\Utils\WeChat\Staff\Staff
     */
    protected $staff;

    /**
     * MessageBuilder constructor.
     *
     * @param \WeChat\Utils\WeChat\Staff\Staff $staff
     */
    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
    }

    /**
     * Set message to send.
     *
     * @param string|AbstractMessage $message
     *
     * @return MessageBuilder
     *
     * @throws InvalidArgumentException
     */
    public function message($message)
    {
        if (is_string($message)) {
            $message = new Text(['content' => $message]);
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Set staff account to send message.
     *
     * @param string $account
     *
     * @return MessageBuilder
     */
    public function by($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Set target user open id.
     *
     * @param string $openId
     *
     * @return MessageBuilder
     */
    public function to($openId)
    {
        $this->to = $openId;

        return $this;
    }

    /**
     * Send the message.
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function send()
    {
        if (empty($this->message)) {
            throw new RuntimeException('No message to send.');
        }

        $transformer = new Transformer();

        if ($this->message instanceof RawMessage) {
            $message = $this->message->get('content');
        } else {
            $content = $transformer->transform($this->message);
            $message = [
                'touser' => $this->to,
            ];

            if ($this->account) {
                $message['customservice'] = ['kf_account' => $this->account];
            }

            $message = array_merge($message, $content);
        }

        return $this->staff->send($message);
    }

    /**
     * Return property.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
