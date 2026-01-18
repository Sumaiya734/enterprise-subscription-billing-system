<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormSubmitted;
use App\Models\CustomerMessage;

class PublicContactController extends Controller
{
    /**
     * Handle public contact form submission
     */
    public function submit(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|min:10|max:5000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Public contact form validation failed', ['errors' => $e->validator->errors()->toArray()]);
            return redirect()->back()
                ->withErrors($e->validator->errors())
                ->withInput();
        }

        // Create customer message from contact form
        // Since the user is not authenticated, we'll use a default customer_id or set it to null
        // For now, we'll set customer_id to null since the user is not logged in
        $message = CustomerMessage::create([
            'message_id' => CustomerMessage::generateMessageId(),
            'customer_id' => null, // No customer associated since not logged in
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => 'Website Contact Form Submission',
            'message' => $validated['message'],
            'category' => 'other',
            'priority' => 'normal',
            'status' => 'open',
            'department' => 'other',
        ]);

        // Send email notification
        try {
            Mail::to(config('mail.from.address', 'support@nanosoft.com'))
                ->cc([$validated['email']])
                ->send(new ContactFormSubmitted($message, $validated));
        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage());
        }

        // Store success message in session
        $request->session()->flash('contact_success', true);
        $request->session()->flash('message_id', $message->message_id);

        return redirect()->back()
            ->with('success', 'Your message has been sent successfully! We have created message ID ' . $message->message_id . ' for tracking.');
    }
}