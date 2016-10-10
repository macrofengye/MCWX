<?php
namespace Core\Utils;


class FuncUtils
{
    /**
     * 生成用户的salt加密串
     *
     * @param int $len
     * @param int $type (1=>数字 , 2=>字母 , 3=>混合)
     * @return string
     */
    public static function generateSalt($len = 32, $type = 3)
    {
        $arr[1] = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $arr[2] = ["b", "c", "d", "f", "g", "h", "j", "k", "m", "n", "p", "q", "r", "s", "t", "u", "v", "w", "x", "z"];
        $arr[3] = ["b", "c", "d", "f", "g", "h", "j", "k", "m", "n", "p", "q", "r", "s", "t", "u", "v", "w", "x", "z", "2", "3", "4", "5", "6", "7", "8", "9"];
        $word = '';
        $cnt = count($arr[$type]) - 1;
        srand((float)microtime() * 1000000);
        shuffle($arr[$type]);
        for ($i = 0; $i < $len; $i++) {
            $word .= $arr[$type][\Zend\Math\Rand::getInteger(0, $cnt, false)];
        }
        if (strlen($word) > $len) {
            $word = substr($word, 0, $len);
        }
        return $word;
    }
}