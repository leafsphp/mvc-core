<?php

if (!function_exists('_env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param  mixed  $default
     * @return mixed
     */
    function _env(string $key, $default = null)
    {
        $item = getenv($key);

        if (!$item) {
            $item = $_ENV[$key] ?? $default;
        }

        return $item;
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [])
    {
        if (ViewConfig('render')) {
            if (ViewConfig('config')) {
                call_user_func_array(ViewConfig('config'), [[
                    'views_path' => AppConfig('views.path'),
                    'cache_path' => AppConfig('views.cachePath'),
                ]]);
            }

            return ViewConfig('render')($view, $data);
        }

        $engine = ViewConfig('view_engine');

        $className = strtolower(get_class(new $engine));

        $fullName = explode('\\', $className);
        $className = $fullName[count($fullName) - 1];

        if (forward_static_call_array(['Leaf\\View', $className], [])) {
            forward_static_call_array(['Leaf\\View', $className], [])->configure(AppConfig('views.path'), AppConfig('views.cachePath'));
            return forward_static_call_array(['Leaf\\View', $className], [])->render($view, $data);
        }

        $engine = new $engine($engine);
        $engine->config(AppConfig('views.path'), AppConfig('views.cachePath'));

        return $engine->render($view, $data);
    }
}
