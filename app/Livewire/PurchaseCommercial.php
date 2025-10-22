<?php

namespace App\Livewire;

use App\Models\Paiement;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseCommercial extends Component
{
    use WithPagination;

    public $sortField = 'paiements.id';
    public $sortDirection = 'desc';
    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** 🔽 Gérer le tri ASC/DESC */
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
            ->with([
                'purchase.customer.pointSale.vendor',
                'purchase.product',
                'purchase.customProduct',
            ])
            ->whereHas('purchase.customer', fn($q) => $q->whereNotNull('point_sale_id'))
            ->when($this->search, function ($query) {
                $query->whereHas('purchase.customer', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('commercial_code', 'like', "%{$this->search}%");
                })
                    ->orWhereHas('purchase.product', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            });

        // 🔹 Gestion dynamique du tri
        switch ($this->sortField) {
            case 'point_sales.name':
                $query->join('purchases', 'purchases.id', '=', 'paiements.purchase_id')
                    ->join('customers', 'customers.id', '=', 'purchases.customer_id')
                    ->join('point_sales', 'point_sales.id', '=', 'customers.point_sale_id')
                    ->select('paiements.*')
                    ->orderBy('point_sales.name', $this->sortDirection);
                break;

            case 'customers.name':
                $query->join('purchases', 'purchases.id', '=', 'paiements.purchase_id')
                    ->join('customers', 'customers.id', '=', 'purchases.customer_id')
                    ->select('paiements.*')
                    ->orderBy('customers.name', $this->sortDirection);
                break;
            case 'products.name':
                $query->join('purchases', 'purchases.id', '=', 'paiements.purchase_id')
                    ->join('products', 'products.id', '=', 'purchases.product_id')
                    ->select('paiements.*')
                    ->orderBy('products.name', $this->sortDirection);
                break;
            case 'vendors.name':
                $query->join('purchases', 'purchases.id', '=', 'paiements.purchase_id')
                    ->join('vendors', 'products.id', '=', 'purchases.product_id')
                    ->select('paiements.*')
                    ->orderBy('products.name', $this->sortDirection);
                break;

            default:
                $query->orderBy($this->sortField, $this->sortDirection);
                break;
        }

        $items = $query->paginate(20);

        return view('livewire.purchase-commercial', compact('items'));
    }

}

