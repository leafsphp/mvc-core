<?php

namespace App\Controllers\Contact;

use App\Mailers\ContactMailer;

class ContactController extends Controller
{
    public function show()
    {
        response()->inertia('contact', [
            'sent' => request()->get('sent') === '1',
            'errors' => flash()->display('errors') ?? [],
            'old' => flash()->display('old') ?? [],
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
                ->withFlash('errors', request()->errors())
                ->withFlash('old', request()->body())
                ->redirect('/contact', 303);
        }

        ContactMailer::message($data)->send();

        response()->redirect('/contact?sent=1', 303);
    }
}
