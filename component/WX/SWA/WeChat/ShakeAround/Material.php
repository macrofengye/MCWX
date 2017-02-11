<?php


namespace MComponent\WX\SWA\WeChat\ShakeAround;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;
use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Class Material.
 */
class Material extends AbstractAPI
{
    const API_MATERIAL_ADD = 'https://api.weixin.qq.com/shakearound/material/add';

    /**
     * Upload image material.
     *
     * @param string $path
     * @param string $type
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function uploadImage($path, $type = 'icon')
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        $type = strtolower($type);

        return $this->parseJSON('upload', [self::API_MATERIAL_ADD, ['media' => $path], [], ['type' => $type]]);
    }
}
