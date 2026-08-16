<?php

namespace App\Controllers\Auth;

class LoginController extends Controller
{
    public function show()
    {
        $form = flash()->display('form') ?? [];

        return response()->view('pages.auth.login', array_merge($form, [
            'errors' => flash()->display('error') ?? [],
        ]));
    }

    public function store()
    {
        $data = request()->validate([
            'email' => 'email',
            'password' => 'min:8',
        ]);

        if (!$data) {
            return response()
                ->withFlash('form', request()->body())
                ->withFlash('error', request()->errors())
                ->redirect('/auth/login');
        }

        $success = auth()->login($data);

        if (!$success) {
            return response()
                ->withFlash('form', request()->body())
                ->withFlash('error', request()->errors())
                ->redirect('/auth/login');
        }

        return response()->redirect('/dashboard');
    }

    public function logout()
    {
        auth()->logout('/');
    }
}
