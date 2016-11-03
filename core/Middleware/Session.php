<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-9-8
 * Time: 上午8:27
 */

namespace Core\Middleware;


final class Session
{
    public static function regenerate()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Get session.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $array = $_SESSION;

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Set session.
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Delete session.
     *
     * @param $keys
     */
    public function delete($keys)
    {
        $original = $_SESSION;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $original)) {
                unset($original[$key]);
                continue;
            }

            $parts = explode('.', $key);
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    public function clearAll()
    {
        $_SESSION = [];
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __isset($key)
    {
        return array_key_exists($key, $_SESSION);
    }

    public function __unset($key)
    {
        $this->delete($key);
    }
}