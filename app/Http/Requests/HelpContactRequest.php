<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for POST /help/contact.
 *
 * Authorisation is delegated to the `perm` middleware on the route, so
 * authorize() returns true here. The request rules enforce both a sane
 * subject + message length and an upper bound (16 KB) so an attacker
 * can't dump payloads into our log via Log::info('help.contact', …).
 */
class HelpContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:3', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:16000'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Please enter a subject so we know what this is about.',
            'subject.max'      => 'Subject should be 120 characters or fewer.',
            'message.required' => 'Please describe what you need help with.',
            'message.min'      => 'Message is too short — please add a bit more detail.',
            'message.max'      => 'Message is too long — please keep it under 16,000 characters.',
        ];
    }
}
