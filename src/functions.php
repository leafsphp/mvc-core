<?php

if (!function_exists('assets')) {
    /**
     * Import an asset
     */
    function assets($assets = null)
    {
        return trim(AppPaths('assets'), '/') . '/' . trim($assets, '/');
    }
}

if (!function_exists('view')) {
    /**
     * Render a view
     *
     * @return string
     */
    function view(string $view, array $data = [])
    {
        if (ViewConfig('render')) {
            if (ViewConfig('config')) {
                call_user_func_array(ViewConfig('config'), [[
                    'views' => AppConfig('views.path'),
                    'cache' => AppConfig('views.cachePath'),
                ]]);
            }

            return ViewConfig('render')($view, $data);
        }

        $engine = ViewConfig('viewEngine');

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
