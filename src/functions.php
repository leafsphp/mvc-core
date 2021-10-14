<?php

if (!function_exists('_env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function _env($key, $default = null)
    {
        $item = getenv($key);

        if (!isset($_ENV[$key]) || (isset($_ENV[$key]) && $_ENV[$key] == null)) {
            $item = $default;
        }

        return $item;
    }
}

if (!function_exists("render")) {
    function render(string $view, array $data = [])
    {
        return ViewConfig("render")($view, $data);
    }
}
