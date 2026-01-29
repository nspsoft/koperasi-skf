<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::all();
        return view('document_templates.index', compact('templates'));
    }

    public function edit(DocumentTemplate $documentTemplate)
    {
        return view('document_templates.edit', compact('documentTemplate'));
    }

    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
        ]);

        $documentTemplate->update([
            'code' => $request->code,
            'name' => $request->name,
            // We can add content editing here later if needed, but risky for average users
        ]);

        return redirect()->route('document-templates.index')
            ->with('success', 'Template berhasil diperbarui!');
    }
}
