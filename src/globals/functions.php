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
     * Get a route by name
     */
    function route()
    {
        $args = func_get_args();

        $routeName = array_shift($args);
        $routeParams = count($args) > 0 ? $args : [];

        $route = app()->route($routeName);

        # check if it has args to replace
        if (preg_match_all('/\{([^}]+)\}/', $route, $matches)) {
            foreach ($matches[1] as $key => $paramName) {
                if (isset($routeParams[$key])) {
                    $route = str_replace('{' . $paramName . '}', $routeParams[$key], $route);
                } else {
                    // Handle missing parameters
                    throw new InvalidArgumentException("Missing parameter '$paramName' for route '$routeName'.");
                }
            }
        }

        return $route;
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
