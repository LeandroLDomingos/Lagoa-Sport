<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Carregando agendamentos de arquivo personalizado
        $config = require base_path('app/Console/Commands/schedule.php');

        foreach ($config['tasks'] as $task) {
            $command = $task['command'];
            $frequency = $task['frequency'];

            switch ($frequency) {
                case 'dailyAt':
                    $time = $task['time'] ?? '00:00';
                    $schedule->command($command)->dailyAt($time);
                    break;
                case 'daily':
                    $schedule->command($command)->daily();
                    break;
                case 'hourly':
                    $schedule->command($command)->hourly();
                    break;
                case 'everyMinute':
                    $schedule->command($command)->everyMinute();
                    break;
                // Adicione mais conforme necessário
            }
        }
    }
}
