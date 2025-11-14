<?php

namespace Leaf;

/**
 * Leaf base controller
 * -----------------
 * Base controller for Leaf PHP Framework
 *
 * @author Michael Darko <mickdd22@gmail.com>
 * @since 1.4.0
 * @version 2.1
 */
class Controller
{
    public $request;
    public $response;
    public $view;
    protected $services = [];

    public function __construct()
    {
        $this->request = new Http\Request;
        $this->response = new Http\Response;

        $this->injectServices();
    }

    /**
     * Return the leaf auth object
     * @return \Leaf\Auth
     */
    public function auth()
    {
        if (!(\Leaf\Config::getStatic('auth.instance'))) {
            \Leaf\Config::set('auth.instance', new \Leaf\Auth());
        }

        return \Leaf\Config::get('auth.instance');
    }

    /**
     * Validate the incoming request with the given rules.
     * @param array $rules The rules to validate against
     */
    public function validate(array $rules)
    {
        return $this->request->validate($rules);
    }

    /**
     * Get the currently authenticated user.
     */
    public function user()
    {
        return $this->auth()->user();
    }

    /**
     * Get the currently authenticated user's ID.
     */
    public function id()
    {
        return $this->auth()->id();
    }

    /**
     * Return a view with the given data.
     */
    public function view(string $view, array $data = [])
    {
        return view($view, $data);
    }

    /**
     * Render a view
     */
    public function render(string $view, array $data = [])
    {
        response()->markup($this->view($view, $data));
    }

    /**
     * Get auth, session and validation errors
     */
    public function errors()
    {
        return (object) [
            'auth' => $this->auth()->errors(),
            'validation' => $this->request->errors()
        ];
    }

    protected function getImports(\ReflectionClass $ref): array
    {
        $file = file($ref->getFileName());
        $imports = [];

        foreach ($file as $line) {
            if (preg_match('/^use\s+([^;]+);/', trim($line), $m)) {
                $fqcn = trim($m[1]);
                $short = basename(str_replace('\\', '/', $fqcn));
                $imports[$short] = $fqcn;
            }

            if (strpos(trim($line), 'class ') === 0) {
                break;
            }
        }

        return $imports;
    }


    protected function injectServices()
    {
        $ref = new \ReflectionClass(static::class);
        $doc = $ref->getDocComment();

        if (!$doc) {
            return;
        }

        $imports = $this->getImports($ref);

        preg_match_all('/@property\s+([\w\\\\]+)\s+\$([\w]+)/', $doc, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            list($full, $className, $name) = $match;

            if (!str_contains($className, '\\')) {
                $className = isset($imports[$className]) ? $imports[$className] : $ref->getNamespaceName() . "\\" . $className;
            }

            if (class_exists($className)) {
                $this->services[$name] = make($className);
            }
        }
    }


    public function __get(string $name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        trigger_error("Undefined property: " . static::class . "::$" . $name, E_USER_WARNING);

        return null;
    }
}
