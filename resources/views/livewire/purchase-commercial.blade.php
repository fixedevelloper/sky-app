<div class="nk-content-wrap">
    <div class="nk-block-head">
        <div class="nk-block-between align-items-center">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Ventes Commerciales</h3>
                <p class="text-soft">Nombre total : {{ $items->total() }}</p>
            </div>

            <div class="nk-block-head-content">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="form-control"
                       placeholder="Rechercher un client, produit ou code commercial...">
            </div>
        </div>
    </div>

    <div class="card card-bordered card-stretch mt-3">
        <div class="card-inner-group">
            <div class="card-inner p-0">
                <div class="card-body">
                    <table class="table nk-tb-list nk-tb-ulist">
                        <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th></th>

                            {{-- 🧭 Tri par nom client --}}
                            <th wire:click="sortBy('customers.name')" class="cursor-pointer">
                                Nom du client
                                @include('components.sort-icon', ['field' => 'customers.name'])
                            </th>

                            <th>Téléphone</th>
                            <th>Localisation</th>

                            {{-- 🧭 Tri par nom point de vente --}}
                            <th >
                                Point de vente
                            </th>

                            <th>Code commercial</th>

                            {{-- 🧭 Tri par manager --}}
                            <th>
                                Manager
                            </th>

                            {{-- 🧭 Tri par produit --}}
                            <th wire:click="sortBy('products.name')" class="cursor-pointer">
                                Article
                                @include('components.sort-icon', ['field' => 'products.name'])
                            </th>

                            {{-- 🧭 Tri par montant --}}
                            <th wire:click="sortBy('paiements.amount')" class="cursor-pointer">
                                Montant
                                @include('components.sort-icon', ['field' => 'paiements.amount'])
                            </th>

                            <th>Style d'achat</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($items as $item)
                            <tr class="nk-tb-item">
                                <td>
                                    <input type="checkbox" class="form-check-input">
                                </td>

                                <td>{{ optional($item->purchase->customer)->name ?? '-' }}</td>
                                <td>{{ optional($item->purchase->customer)->phone ?? '-' }}</td>
                                <td>{{ optional($item->purchase->customer)->localisation ?? '-' }}</td>
                                <td>{{ optional($item->purchase->customer->pointSale)->name ?? '-' }}</td>
                                <td>{{ optional($item->purchase->customer)->commercial_code ?? '-' }}</td>
                                <td>{{ optional($item->purchase->customer->pointSale->vendor)->name ?? '-' }}</td>
                                <td>{{ optional($item->purchase->product)->name ?? optional($item->customProduct)->name ?? '-' }}</td>
                                <td>{{ number_format(optional($item->purchase->product)->price ?? optional($item->customProduct)->amount ?? 0, 0, ',', ' ') }} F</td>
                                <td>{{ ucfirst($item->purchase->type ?? 'standard') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">
                                    Aucune vente trouvée
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


