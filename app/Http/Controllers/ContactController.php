<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessageMail;
use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\Setting;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public const BUDGETS = ['lt5', '5to15', '15to50', 'gt50', 'unsure'];

    public function show(Request $request)
    {
        return view('contact', [
            'services' => Service::active()->ordered()->get(),
            'budgets' => self::BUDGETS,
            'selectedService' => $request->query('service'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service' => ['nullable', 'string', 'max:120'],
            'budget' => ['nullable', 'in:' . implode(',', self::BUDGETS)],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['nullable', 'max:0'], // honeypot: real people leave this hidden field empty
        ]);

        unset($data['website']);

        $message = ContactMessage::create([
            ...$data,
            'locale' => app()->getLocale(),
            'ip' => $request->ip(),
        ]);

        $to = Setting::get('notify_email') ?: Setting::get('email') ?: config('mail.from.address');

        try {
            Mail::to($to)->send(new NewContactMessageMail($message));
        } catch (\Throwable $e) {
            // The message is already saved in the admin inbox; a failed email must not break the form.
            Log::error('Contact notification email failed: ' . $e->getMessage(), ['contact_message_id' => $message->id]);
        }

        $whatsapp = WhatsApp::chatUrl(__('site.contact.whatsapp_followup', ['name' => $message->name]));

        return redirect()->route('contact')
            ->with('sent', true)
            ->with('whatsapp', $whatsapp);
    }
}
