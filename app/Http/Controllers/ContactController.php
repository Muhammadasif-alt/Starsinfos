<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $message = ContactMessage::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ]));

        // The message is already saved, so a mail outage must not fail the visitor's request.
        try {
            Mail::to(config('mail.admin_address'))->send(new ContactMessageReceived($message));
        } catch (Throwable $exception) {
            report($exception);
        }

        return back()->with('contact_status', 'Shukriya! Aap ka message mil gaya hai.');
    }
}
