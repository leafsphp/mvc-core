<?php

namespace App\Controllers\Contact;

use App\Mailers\ContactMailer;

class ContactController extends Controller
{
    public function show()
    {
        response()->render('pages.contact', [
            'sent' => request()->get('sent') === '1',
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
            return response()->render('pages.contact', [
                'sent' => false,
                'errors' => request()->errors(),
                'old' => request()->body(),
            ]);
        }

        ContactMailer::message($data)->send();

        response()->redirect('/contact?sent=1', 303);
    }
}
