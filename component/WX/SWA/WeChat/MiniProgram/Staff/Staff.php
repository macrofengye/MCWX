<?php

namespace MComponent\WX\SWA\WeChat\MiniProgram\Staff;

use MComponent\WX\SWA\WeChat\Staff\Staff as BaseStaff;

class Staff extends BaseStaff
{
    public function __construct()
    {
        $accessToken = func_get_args()[0];

        parent::__construct($accessToken);
    }
}
