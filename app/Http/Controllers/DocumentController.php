<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    /**
     * Exibe a página de upload de documentos.
     */
    public function index(): Response
    {
        $documents = Auth::user()->documents;
        return Inertia::render('documents/Index', compact('documents'));
    }

    /**
     * Faz o upload dos documentos de identidade e comprovante de residência.
     */
    public function store(Request $request)
    {
        $request->validate([
            'documents.*.type' => 'required|in:identity,residence_proof',
            'documents.*.file' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $user = Auth::user();

        foreach ($request->file('documents') as $document) {
            $filePath = $document['file']->store("documents/{$user->id}", 'public');

            Document::create([
                'user_id' => $user->id,
                'type' => $document['type'],
                'file_path' => $filePath,
                'status' => 'pending',
            ]);
        }

        return redirect()->back()->with('message', 'Documentos enviados para análise.');
    }

    /**
     * Aprova ou rejeita um documento.
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $document->update(['status' => $request->status]);

        return redirect()->back()->with('message', 'Status do documento atualizado.');
    }

    /**
     * Permite o download dos documentos.
     */
    public function download(Document $document)
    {
        if (Auth::id() !== $document->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::download("public/{$document->file_path}");
    }
}
