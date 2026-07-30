<?php

namespace App\Controllers\Contact;

use App\Mailers\ContactMailer;

class ContactController extends Controller
{
    public function show()
    {
        $form = flash()->display('form') ?? [];

        response()->inertia('contact', [
            'sent' => request()->get('sent') === '1',
            'errors' => flash()->display('error') ?? [],
            'old' => $form,
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

        response()->redirect('/contact?sent=1', 303);
    }
}
