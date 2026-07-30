<?php

namespace App\Controllers\Contact;

use App\Mailers\ContactMailer;

class ContactController extends Controller
{
    public function show()
    {
        response()->inertia('contact', [
            'sent' => flash()->display('sent') ?? false,
            'errors' => flash()->display('error') ?? [],
            'old' => flash()->display('form') ?? [],
        ]);
    }

    public function submit()
    {
        $data = request()->validate([
            'name' => 'string|min:2',
            'email' => 'email',
            'message' => 'string|min:10',
        ]);

        if (!$data) {
            return response()
                ->withFlash('form', request()->body())
                ->withFlash('error', request()->errors())
                ->redirect('/contact', 303);
        }

        ContactMailer::message($data)->send();

        response()
            ->withFlash('sent', true)
            ->redirect('/contact', 303);
    }
}
