<?php

if (!function_exists('assets')) {
    /**
     * Import an asset
     * @param string $assets The asset to import
     */
    function assets($assets = '')
    {
        return trim(AppPaths('assets'), '/') . '/' . trim($assets, '/');
    }
}

if (!function_exists('view')) {
    /**
     * Return a view
     *
     * @param string $view The view to render
     * @param array $data The data to pass to the view
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

if (!function_exists('render')) {
    /**
     * Render a view
     *
     * @param string $view The view to render
     * @param array $data The data to pass to the view
     */
    function render(string $view, array $data = [])
    {
        (new \Leaf\Http\Response())->markup(view($view, $data));
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a given url
     *
     * @param string $url The url to redirect to
     */
    function redirect(string $url)
    {
        return (new \Leaf\Http\Response())->redirect($url);
    }
}

if (!function_exists('route')) {
    /**
     * Get a route by name
     * @param string $route The route to get
     */
    function route(string $route)
    {
        return app()->route($route);
    }
}
