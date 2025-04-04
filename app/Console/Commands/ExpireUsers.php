<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;

class ExpireUsers extends Command
{
    protected $signature = 'users:expire';
    protected $description = 'Expira usuários aprovados há mais de 6 meses (exceto admin e manager)';

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
            $user->update(['status' => 'pending']);
            $count++;
        }

        $this->info("{$count} usuários expirados (status alterado para 'pending').");
        return 0;
    }
}
