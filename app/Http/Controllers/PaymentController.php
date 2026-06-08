<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function checkout(Event $event)
    {
        if ($event->price <= 0) {
            return redirect()->route('registrations.store', $event);
        }

        $alreadyRegistered = Registration::where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'You are already registered for this event.');
        }

        if ($event->isFull()) {
            return back()->with('error', 'This event is full.');
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'unit_amount'  => $event->price * 100,
                    'product_data' => [
                        'name'        => $event->title,
                        'description' => $event->description ?? 'Event registration — MEGA EVENT',
                        'images'      => $event->cover_image
                            ? [asset('storage/' . $event->cover_image)]
                            : [],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'          => 'payment',
            'success_url'   => route('payment.success', [
                'event'      => $event->id,
                'session_id' => '{CHECKOUT_SESSION_ID}'
            ]),
            'cancel_url'    => route('payment.cancel', $event->id),
            'customer_email'=> auth()->user()->email,
            'metadata'      => [
                'event_id' => $event->id,
                'user_id'  => auth()->id(),
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, Event $event)
    {
        $sessionId = $request->get('session_id');

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                Registration::firstOrCreate(
                    [
                        'user_id'  => auth()->id(),
                        'event_id' => $event->id,
                    ],
                    [
                        'status'        => 'confirmed',
                        'registered_at' => now(),
                    ]
                );

                return redirect()
                    ->route('participant.dashboard')
                    ->with('success', '🎉 Payment successful! You are now registered for ' . $event->title);
            }

            return redirect()
                ->route('events.show', $event)
                ->with('error', 'Payment was not completed.');

        } catch (\Exception $e) {
            return redirect()
                ->route('events.show', $event)
                ->with('error', 'Payment verification failed.');
        }
    }

    public function cancel(Event $event)
    {
        return redirect()
            ->route('events.show', $event)
            ->with('error', 'Payment was cancelled. You have not been registered.');
    }
}
