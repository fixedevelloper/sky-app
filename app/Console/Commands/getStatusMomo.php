<?php

namespace App\Console\Commands;

use App\Service\MomoService;
use Illuminate\Console\Command;

use App\Models\Paiement;

class GetStatusMomo extends Command
{
    protected $signature = 'app:get-status-momo';
    protected $description = 'Vérifie et met à jour le statut des paiements MoMo en attente.';

    private MomoService $momo;

    public function __construct(MomoService $momo)
    {
        parent::__construct();
        $this->momo = $momo;
    }

    public function handle(): int
    {
        $paiements = Paiement::query()->where('status', 'pending')->get();

        if ($paiements->isEmpty()) {
            $this->info('Aucun paiement en attente trouvé.');
            return Command::SUCCESS;
        }

        foreach ($paiements as $paiement) {
            $this->info("Vérification du paiement #{$paiement->id} ({$paiement->reference_id}) ...");

            try {
                $statusResponse = $this->momo->getPaymentStatus($paiement->reference_id);
                $status = $statusResponse['status'];

                $paiement->update([
                    'status' => match ($status) {
                    'SUCCESSFUL' => 'confirmed',
                        'FAILED'     => 'failed',
                        default      => 'pending',
                    },
                    'confirmed_at' => $status === 'SUCCESSFUL' ? now() : $paiement->confirmed_at,
                ]);

                $this->line("→ Statut mis à jour : {$paiement->status}");
            } catch (\Exception $e) {
                $this->error("Erreur pour le paiement #{$paiement->id} : " . $e->getMessage());
                continue;
            }
        }

        $this->info('✅ Vérification des paiements terminée.');
        return Command::SUCCESS;
    }
}
