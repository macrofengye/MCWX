<?php

namespace MComponent\WX\EWA\WeChat;

use MComponent\WX\EWA\WeChat\Utils\Bag;

class Input extends Bag
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct(array_merge($_GET, $_POST));
    }
}
