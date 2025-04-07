<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
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

    public function analising(): Response
    {
        $users = User::where('status', 'is_analising')
            ->with([
                'documents' ])
            ->get();
        return Inertia::render('users/Analising', compact( 'users'));
    }

    public function is_analising(): Response
    {
        $documents = Auth::user()->documents;
        return Inertia::render('documents/IsAnalising');
    }

    /**
     * Faz o upload dos documentos de identidade e comprovante de residência.
     */
    public function store(Request $request)
    {
        $request->validate([
            'documents.*.type' => 'required|in:identity,residence_proof,identity-front,identity-back',
            'documents.*.file' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $user->update(['status' => 'is_analising']);
        // Armazena os arquivos no disco 'local', que é privado.
        foreach ($request->documents as $document) {
            $filePath = $document['file']->store("documents/{$user->id}", 'local');
            Document::create([
                'user_id' => $user->id,
                'type' => $document['type'],
                'file_path' => $filePath,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('documents.is_analising')
            ->with('flash.error', 'Seu cadastro está sendo analisado. Por favor, aguarde a validação.');
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
        // Permite o download se o usuário for o dono do documento ou um admin.
        if (Auth::id() !== $document->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Verifica no disco privado (local) se o arquivo existe.
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        // Realiza o download do arquivo do disco 'local'.
        return Storage::disk('local')->download($document->file_path);
    }
}
