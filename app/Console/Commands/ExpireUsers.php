<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ExpireUsers extends Command
{
    protected $signature = 'users:expire';
    protected $description = 'Expira usuários aprovados há mais de 6 meses (exceto admin e manager) e exclui documentos de residence_proof';

    public function handle()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        $users = User::where('status', 'active')
            ->whereNotIn('id', function ($query) {
                $query->select('user_id')
                    ->from('role_user')
                    ->whereIn('role_id', function ($subQuery) {
                        $subQuery->select('id')
                            ->from('roles')
                            ->whereIn('name', ['admin', 'manager']);
                    });
            })
            ->where('approved_at', '<=', $sixMonthsAgo)
            ->get();
        
        $count = 0;

        foreach ($users as $user) {
            // Excluir documentos do tipo residence_proof
            $residenceDocs = $user->documents()->where('type', 'residence_proof')->get();
            foreach ($residenceDocs as $doc) {
                // Verifica se o arquivo existe no disco 'private' e o exclui fisicamente
                if (Storage::disk('local')->exists($doc->file_path)) {
                    Storage::disk('local')->delete($doc->file_path);
                }
                // Remove o documento do banco de dados
                $doc->delete();
            }
            
            // Atualiza o status do usuário para 'pending' e limpa approved_at
            $user->update(['status' => 'pending', 'approved_at' => null]);
            $count++;
        }

        $this->info("{$count} usuários expirados (status alterado para 'pending' e approved_at limpo) e documentos de residence_proof excluídos.");
        return 0;
    }
}
