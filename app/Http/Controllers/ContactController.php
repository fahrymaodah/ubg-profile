<?php

namespace App\Http\Controllers;

use App\Enums\UnitType;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display contact page.
     */
    public function index(Request $request): View
    {
        return view('contact.index');
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request): RedirectResponse
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Check honeypot BEFORE validation (anti-spam)
        // Bots usually fill all fields including hidden ones
        if (!empty($request->input('website'))) {
            // Silently reject spam - return success to not alert spammer
            return back()->with('success', 'Pesan Anda telah terkirim. Terima kasih!');
        }

        // Use strict email validation in production, basic in testing
        $emailRule = app()->environment('testing') 
            ? 'required|email|max:255' 
            : 'required|email:rfc,dns|max:255';
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'email' => $emailRule,
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'subject' => 'required|string|max:255|min:5',
            'message' => 'required|string|max:5000|min:10',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'subject.min' => 'Subjek minimal 5 karakter.',
            'message.required' => 'Pesan wajib diisi.',
            'message.min' => 'Pesan minimal 10 karakter.',
        ]);

        ContactMessage::create([
            'unit_type' => $unitType,
            'unit_id' => $unitId,
            'name' => strip_tags($validated['name']),
            'email' => $validated['email'],
            'phone' => isset($validated['phone']) ? strip_tags($validated['phone']) : null,
            'subject' => strip_tags($validated['subject']),
            'message' => strip_tags($validated['message']),
            'status' => 'unread',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Pesan Anda telah terkirim. Terima kasih!');
    }
}
