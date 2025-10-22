<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PointSale as SalePoint;
use Livewire\WithPagination;


class PointSale extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['search', 'sortField', 'sortDirection', 'page'];

    public function updatingSearch()
    {
        $this->resetPage(); // 🔄 remet la pagination à 1 quand on cherche
    }

    /** 🔹 Fonction pour changer le tri */
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
        $items = SalePoint::with('vendor')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vendor', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.point-sale', compact('items'));
    }
}
