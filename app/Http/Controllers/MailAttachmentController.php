<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MailAttachmentController extends Controller
{
    public function show(Request $request, $user, $token)
    {
        abort_unless((string) $request->user()->id === (string) $user, 403);
        abort_unless(preg_match('/^[a-f0-9-]{36}$/', $token), 404);
        $path = 'mail-attachments/' . $user . '/' . $token;
        abort_unless(Storage::disk('local')->exists($path), 404);
        $name = basename(str_replace('\\', '/', (string) $request->query('name', 'attachment')));
        return Storage::disk('local')->download($path, $name, ['X-Content-Type-Options' => 'nosniff', 'Content-Type' => 'application/octet-stream']);
    }
}
