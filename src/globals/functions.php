<?php

if (!function_exists('assets')) {
    /**
     * Import an asset
     * @param string $assets The asset to import
     */
    function assets($assets = '')
    {
        return PublicPath('assets') . '/' . trim($assets, '/');
    }
}

if (!function_exists('fake')) {
    /**
     * Return a faker instance
     * @return \Faker\Generator
     */
    function fake()
    {
        return \Faker\Factory::create();
    }
}

if (!function_exists('view')) {
    /**
     * Return a view
     *
     * @param string $view The view to render
     * @param array|object $data The data to pass to the view
     *
     * @return string
     */
    function view(string $view, $data = [])
    {
        return app()->template()->render($view, $data);
    }
}

if (!function_exists('render')) {
    /**
     * Render a view
     *
     * @param string $view The view to render
     * @param array|object $data The data to pass to the view
     */
    function render(string $view, $data = [])
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
     * Get a route URL by name. Params fill the route's placeholders —
     * positionally (route('users.show', 5)) or by name
     * (route('users.show', ['id' => 5])).
     */
    function route(string $routeName, ...$params)
    {
        // named params: hand straight to the router, which understands
        // optional params and inline constraints
        if (count($params) === 1 && is_array($params[0])) {
            return app()->route($routeName, $params[0]);
        }

        if (empty($params)) {
            return app()->route($routeName);
        }

        // positional params: map onto the placeholders in order
        $pattern = app()->route($routeName);
        preg_match_all('/\{([^}:?]+)(\?|:[^}]*)?\}/', $pattern, $matches);

        $named = [];

        foreach ($matches[1] as $index => $paramName) {
            if (array_key_exists($index, $params)) {
                $named[$paramName] = $params[$index];
            }
        }

        return app()->route($routeName, $named);
    }
}

if (!function_exists('vite')) {
    /**
     * Get a route by name
     * @param string|array $route The route to get
     * @param string $baseDir The base directory to look for the file(s)
     */
    function vite($files, $baseDir = 'app/views')
    {
        if (is_array($files)) {
            $files = array_map(function ($file) use ($baseDir) {
                if (strpos($file, $baseDir) !== false) {
                    return $file;
                }

                return trim($baseDir, '/') . '/' . ltrim($file, '/');
            }, $files);
        } else if (is_string($files)) {
            if (strpos($files, $baseDir) === false) {
                $files = trim($baseDir, '/') . '/' . ltrim($files, '/');
            }

            $files = [$files];
        }

        return \Leaf\Vite::build($files);
    }
}
