<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecureDownloadController extends Controller
{
    /**
     * Show the password form for a secure download
     */
    public function showPasswordForm(string $slug)
    {
        $link = Link::where('slug', $slug)->firstOrFail();

        return view('secure-download.password-form', [
            'slug' => $slug,
            'email' => $link->email
        ]);
    }

    /**
     * Verify password and show download page or return to password form
     */
    public function verifyPassword(Request $request, string $slug)
    {
        $link = Link::where('slug', $slug)->firstOrFail();

        if ($request->password !== $link->password) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.'
            ]);
        }

        return view('secure-download.download-page', [
            'link' => $link
        ]);
    }

    /**
     * Download the file
     */
    public function download(string $slug)
    {
        $link = Link::where('slug', $slug)
            ->whereHas('file')
            ->firstOrFail();

        $file = $link->file;

        if (!Storage::disk('survey_data')->exists($file->path)) {
            abort(404, 'File not found');
        }

        if (is_null($link->first_used_at)) {
            $link->update(['first_used_at' => now()]);
        }

        return response()->download(
            Storage::disk('survey_data')->path($file->path),
            basename($file->path)
        );
    }

}