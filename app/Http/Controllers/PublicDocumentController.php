<?php

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use Illuminate\Http\Request;

class PublicDocumentController extends Controller
{
    public function verify($id)
    {
        $document = GeneratedDocument::where('id', $id)->first();

        // If not found, show invalid page
        if (!$document) {
            return view('documents.verify', ['isValid' => false]);
        }

        // Use 'data' JSON if available, otherwise minimal info
        $meta = $document->data ?? [];

        // Log the verification (optional update verified_at if first time)
        if (!$document->verified_at) {
            $document->update(['verified_at' => now()]);
        }

        return view('documents.verify', [
            'isValid' => true,
            'document' => $document,
            'meta' => $meta
        ]);
    }
}
