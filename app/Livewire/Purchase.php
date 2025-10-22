<?php

namespace App\Livewire;

use App\Models\Paiement;
use Livewire\Component;
use Livewire\WithPagination;

class Purchase extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['search', 'sortField', 'sortDirection', 'page'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** 🔽 Fonction pour changer le tri */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Paiement::query()
            ->with(['purchase.customer', 'purchase.product', 'purchase.customProduct'])
            ->when($this->search, function ($q) {
                $q->whereHas('purchase.customer', function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('commercial_code', 'like', "%{$this->search}%");
                })
                    ->orWhereHas('purchase.product', function ($q2) {
                        $q2->where('name', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('purchase.customProduct', function ($q2) {
                        $q2->where('name', 'like', "%{$this->search}%");
                    });
            });

        // 👇 Gestion du tri
        if ($this->sortField === 'customer_name') {
            $query->select('paiements.*')
                ->join('purchases', 'paiements.purchase_id', '=', 'purchases.id')
                ->join('customers', 'purchases.customer_id', '=', 'customers.id')
                ->orderBy('customers.name', $this->sortDirection);
        } elseif ($this->sortField === 'product_name') {
            $query->select('paiements.*')
                ->leftJoin('purchases', 'paiements.purchase_id', '=', 'purchases.id')
                ->leftJoin('products', 'purchases.product_id', '=', 'products.id')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'amount') {
            $query->orderBy('amount', $this->sortDirection);
        } else {
            $query->latest();
        }

        $items = $query->paginate(20);

        return view('livewire.purchase', compact('items'));
    }

}
