<?php

namespace App\Http\Controllers;

use App\Models\TemporarySignature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SignatureController extends Controller
{
    /**
     * Create a remote signing session.
     */
    public function createSession(Request $request)
    {
        $token = Str::random(40);
        
        $session = TemporarySignature::create([
            'session_token' => $token,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        return response()->json([
            'session_token' => $token,
            'signing_url' => route('signature.remote', ['token' => $token]),
            'status_url' => route('signature.status', ['token' => $token]),
        ]);
    }

    /**
     * Get signature session status.
     */
    public function getStatus($token)
    {
        $session = TemporarySignature::where('session_token', $token)->first();

        if (!$session) {
            return response()->json(['error' => 'Invalid session'], 404);
        }

        if (Carbon::now()->greaterThan($session->expires_at)) {
            return response()->json(['status' => 'expired']);
        }

        return response()->json([
            'status' => $session->status,
            'signature_data' => $session->signature_data,
        ]);
    }

    /**
     * Renders remote signing page for mobile phone.
     */
    public function remoteSignPage($token)
    {
        $session = TemporarySignature::where('session_token', $token)->first();

        if (!$session || Carbon::now()->greaterThan($session->expires_at)) {
            abort(404, 'Signature session expired or not found.');
        }

        return view('volunteer.components.remote-sign', ['token' => $token]);
    }

    /**
     * Save the signature drawn remotely on the phone.
     */
    public function saveRemoteSignature(Request $request, $token)
    {
        $request->validate([
            'signature_data' => 'required|string',
        ]);

        $session = TemporarySignature::where('session_token', $token)->first();

        if (!$session || Carbon::now()->greaterThan($session->expires_at)) {
            return response()->json(['error' => 'Session expired or not found'], 400);
        }

        $session->update([
            'signature_data' => $request->signature_data,
            'status' => 'completed',
        ]);

        return response()->json(['message' => 'Signature submitted successfully!']);
    }

    /**
     * Direct upload signature.
     */
    public function uploadDirect(Request $request)
    {
        $request->validate([
            'signature_file' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('signature_file')) {
            $path = $request->file('signature_file')->store('signatures', 'public');
            return response()->json([
                'success' => true,
                'path' => '/storage/' . $path,
            ]);
        }

        return response()->json(['error' => 'Upload failed'], 400);
    }
}
