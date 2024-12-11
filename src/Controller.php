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

    public function __construct()
    {
        $this->request = new Http\Request;
        $this->response = new Http\Response;
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
}
