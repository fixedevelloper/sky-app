<div class="nk-content-wrap">
    <div class="nk-block-head">
        <div class="nk-block-between align-items-center">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Achats</h3>
                <p class="text-soft">Nombre total : {{ $items->total() }}</p>
            </div>

            {{-- 🔍 Recherche --}}
            <div class="nk-block-head-content">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="form-control"
                       placeholder="Rechercher par client, téléphone, produit ou référence">
            </div>
        </div>
    </div>

    <div class="card card-bordered card-stretch mt-3">
        <div class="card-inner-group">
            <div class="card-inner p-0">
                <div class="card-body">

                    <table class="nk-tb-list nk-tb-ulist">
                        <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col nk-tb-col-check"></th>

                            <th wire:click="sortBy('customer_name')" class="nk-tb-col cursor-pointer">
                                Client
                                @if($sortField === 'customer_name')
                                    <x-sort-icon :direction="$sortDirection"/>
                                @endif
                            </th>

                            <th class="nk-tb-col">Téléphone</th>

                            <th wire:click="sortBy('product_name')" class="nk-tb-col cursor-pointer">
                                Produit
                                @if($sortField === 'product_name')
                                    <x-sort-icon :direction="$sortDirection"/>
                                @endif
                            </th>

                            <th wire:click="sortBy('amount')" class="nk-tb-col cursor-pointer">
                                Montant
                                @if($sortField === 'amount')
                                    <x-sort-icon :direction="$sortDirection"/>
                                @endif
                            </th>

                            <th wire:click="sortBy('status')" class="nk-tb-col cursor-pointer">
                                Statut
                                @if($sortField === 'status')
                                    <x-sort-icon :direction="$sortDirection"/>
                                @endif
                            </th>

                            <th class="nk-tb-col">Date</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($items as $item)
                            <tr class="nk-tb-item">

                                <td class="nk-tb-col nk-tb-col-check">
                                    <input type="checkbox">
                                </td>

                                {{-- CLIENT --}}
                                <td class="nk-tb-col">
                                    <strong>{{ optional($item->user)->name ?? '-' }}</strong>
                                </td>

                                {{-- TELEPHONE --}}
                                <td class="nk-tb-col">
                                    {{ optional($item->user)->phone ?? '-' }}
                                </td>

                                {{-- PRODUITS --}}
                                <td class="nk-tb-col">
                                    @foreach($item->items as $orderItem)
                                        <div class="text-sm">
                                            {{ $orderItem->product->name ?? '-' }}
                                            <span class="text-muted">
                                                (x{{ $orderItem->quantity }})
                                            </span>
                                        </div>
                                    @endforeach
                                </td>

                                {{-- MONTANT --}}
                                <td class="nk-tb-col">
                                    @if(data_get($item->meta, 'mode') !== 'distribute')
                                        <strong>
                                            {{ number_format($item->amount, 0, ',', ' ') }} F
                                        </strong>
                                    @else
                                        <button class="btn btn-outline-dark">Modifier le prix</button>
                                    @endif
                                </td>


                                {{-- STATUT --}}
                                <td class="nk-tb-col">
                                    <span class="badge
                                        @if($item->status === 'confirmed') bg-success
                                        @elseif($item->status === 'pending') bg-warning
                                        @elseif($item->status === 'failed') bg-danger
                                        @else bg-secondary @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>

                                {{-- DATE --}}
                                <td class="nk-tb-col">
                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Aucun achat trouvé
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('components')
        @if (!class_exists('App\View\Components\SortIcon'))
            @php
                // Si tu veux, crée un vrai composant Blade `x-sort-icon` :
                // php artisan make:component SortIcon
            @endphp
        @endif
    @endpush
    @endonce

