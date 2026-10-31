<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function __invoke(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate(
            ['email' => ['required', 'email:rfc', 'max:255', 'unique:subscribers,email']],
            ['email.unique' => 'This email is already subscribed.'],
        );

        Subscriber::create($validated);

        $message = 'Thanks for subscribing!';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('newsletter_status', $message);
    }
}
