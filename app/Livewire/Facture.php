<?php

namespace App\Livewire;

use App\Models\Purchase;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class Facture extends Component
{
    use WithPagination;

    public $status = ''; // 🔹 Filtre de statut : '', 'soldé', 'en_cours'
    public $perPage = 10; // 🔹 Pagination configurable

    protected $paginationTheme = 'bootstrap'; // Ou 'tailwind' selon ton CSS

    public function updatingStatus()
    {
        $this->resetPage(); // Réinitialise la pagination au changement de filtre
    }

    public function render()
    {
        // 🔹 Base query
       /* $purchases = Purchase::with(['customer', 'product', 'paiements'])
            ->whereNotNull('product_id')
            ->get();

        $items = collect();

        foreach ($purchases as $purchase) {
            $amountPaid = $purchase->paiements
                ->where('status', 'confirmed')
                ->sum('amount');

            $total = optional($purchase->product)->price ?? 0;
            $status = $amountPaid >= $total ? 'Soldé' : 'En cours';

            $items->push([
                'customer' => optional($purchase->customer)->name ?? 'N/A',
                'phone' => optional($purchase->customer)->phone ?? 'N/A',
                'credit_account' => $purchase->id,
                'total' => $total,
                'paid' => $amountPaid,
                'remaining' => max(0, $total - $amountPaid),
                'percent' => $total > 0 ? round(($amountPaid / $total) * 100, 2) : 0,
                'status' => $status,
            ]);
        }

        // 🔹 Application du filtre de statut
        if ($this->status) {
            $items = $items->filter(fn($i) => strtolower($i['status']) === strtolower($this->status));
        }

        // 🔹 Pagination manuelle
        $page = $this->page ?? 1;
        $paginated = $items->forPage($page, $this->perPage);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $items->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );*/

        return view('livewire.facture', ['items' => new Paginator()]);
    }
}

