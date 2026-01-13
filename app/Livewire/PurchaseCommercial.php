<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Paiement;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseCommercial extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'orders.id';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search',
        'sortField',
        'sortDirection',
        'page'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** 🔁 Tri */
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
        $query = Order::query()
            ->with(['user', 'items.product'])

            // 🔍 RECHERCHE
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('user', function ($u) {
                        $u->where('name', 'like', "%{$this->search}%")
                            ->orWhere('phone', 'like', "%{$this->search}%");
                    })
                        ->orWhereHas('items.product', function ($p) {
                            $p->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhere('reference_id', 'like', "%{$this->search}%");
                });
            });

        // 🔽 TRI
        switch ($this->sortField) {

            case 'customer_name':
                $query->join('users', 'orders.user_id', '=', 'users.id')
                    ->orderBy('users.name', $this->sortDirection)
                    ->select('orders.*');
                break;

            case 'product_name':
                $query->join('items', 'orders.id', '=', 'items.order_id')
                    ->join('products', 'items.product_id', '=', 'products.id')
                    ->orderBy('products.name', $this->sortDirection)
                    ->select('orders.*')
                    ->distinct();
                break;

            case 'amount':
                $query->orderBy('orders.amount', $this->sortDirection);
                break;

            case 'status':
                $query->orderBy('orders.status', $this->sortDirection);
                break;

            default:
                $query->orderBy('orders.id', $this->sortDirection);
        }

        $items = $query->paginate(20);

        return view('livewire.purchase', compact('items'));
    }
}


