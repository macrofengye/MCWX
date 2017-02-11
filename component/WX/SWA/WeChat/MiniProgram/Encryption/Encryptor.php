<?php

namespace MComponent\WX\SWA\WeChat\MiniProgram\Encryption;

use MComponent\WX\SWA\WeChat\Encryption\EncryptionException;
use MComponent\WX\SWA\WeChat\Encryption\Encryptor as BaseEncryptor;
use MComponent\WX\SWA\WeChat\Support\Collection;
use Exception as BaseException;

class Encryptor extends BaseEncryptor
{
    /**
     * {@inheritdoc}.
     */
    protected $aesKeyLength = 24;

    /**
     * A non-NULL Initialization Vector.
     *
     * @var string
     */
    protected $iv;

    /**
     * Encryptor constructor.
     *
     * @param string $sessionKey
     * @param string $iv
     */
    public function __construct($sessionKey, $iv)
    {
        $this->iv = base64_decode($iv, true);

        parent::__construct(null, null, $sessionKey);
    }

    /**
     * Decrypt data.
     *
     * @param $encrypted
     *
     * @return string
     */
    public function decryptData($encrypted)
    {
        return $this->decrypt($encrypted);
    }

    /**
     * Decrypt data.
     *
     * @param string $encrypted
     *
     * @return Collection
     *
     * @throws EncryptionException
     */
    private function decrypt($encrypted)
    {
        try {
            $key = $this->getAESKey();
            $ciphertext = base64_decode($encrypted, true);

            $decrypted = openssl_decrypt($ciphertext, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $this->iv);

            $result = $this->decode($decrypted);
        } catch (BaseException $e) {
            throw new EncryptionException($e->getMessage(), EncryptionException::ERROR_DECRYPT_AES);
        }

        $result = json_decode($result, true);

        return new Collection($result);
    }
}
