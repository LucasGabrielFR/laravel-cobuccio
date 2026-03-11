<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Console\Command;

class WalletAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:audit {--fix : Tentativa automática de corrigir saldos divergentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica a integridade financeira cruzando saldos atuais com histórico de transações.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando auditoria de carteiras...');
        $users = User::all();
        $inconsistencies = 0;

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        $results = [];

        foreach ($users as $user) {
            // Soma de depósitos e transferências recebidas
            $totalReceived = $user->receivedTransactions()
                ->where('status', 'completed')
                ->sum('amount');

            // Soma de transferências enviadas e estornos de depósitos enviados como 'reversal'
            $totalSent = $user->sentTransactions()
                ->where('status', 'completed')
                ->sum('amount');

            // Saldo calculado = Entradas - Saídas
            $calculatedBalance = $totalReceived - $totalSent;

            if ($user->balance !== (int) $calculatedBalance) {
                $inconsistencies++;
                $results[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'current' => $user->balance / 100,
                    'calculated' => $calculatedBalance / 100,
                    'diff' => ($user->balance - $calculatedBalance) / 100,
                ];

                if ($this->option('fix')) {
                    $user->update(['balance' => $calculatedBalance]);
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($inconsistencies > 0) {
            $this->error("Detectadas {$inconsistencies} inconsistências no sistema!");
            $this->table(
                ['ID', 'Nome', 'Saldo Atual (R$)', 'Saldo Calculado (R$)', 'Diferença (R$)'],
                $results
            );

            if ($this->option('fix')) {
                $this->warn('As inconsistências foram corrigidas conforme os registros históricos.');
            } else {
                $this->line('Dica: Use --fix para sincronizar os saldos com o histórico de transações.');
            }
        } else {
            $this->info('Auditoria concluída com sucesso! Todos os saldos estão íntegros seguindo o histórico.');
        }

        return 0;
    }
}
