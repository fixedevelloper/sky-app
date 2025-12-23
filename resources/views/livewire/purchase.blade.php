<div class="nk-content-wrap">
    <div class="nk-block-head">
        <div class="nk-block-between align-items-center">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Achats</h3>
                <p class="text-soft">Nombre total : {{ $items->total() }}</p>
            </div>

            {{-- 🔍 Barre de recherche --}}
            <div class="nk-block-head-content">
                <input type="text" wire:model.live.debounce.200ms="search" class="form-control"
                       placeholder="Rechercher par nom, téléphone, code promo ou produit...">
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
                            <th class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                    <input type="checkbox" class="custom-control-input" id="pid-all">
                                    <label class="custom-control-label" for="pid-all"></label>
                                </div>
                            </th>
                            <th wire:click="sortBy('customer_name')" class="nk-tb-col cursor-pointer">
                                <span class="sub-text">Nom du client</span>
                                @if($sortField === 'customer_name') <x-sort-icon :direction="$sortDirection" /> @endif
                            </th>
                            <th class="nk-tb-col">Téléphone</th>
                            <th class="nk-tb-col">Localisation</th>
                            <th class="nk-tb-col">Code promo</th>
                            <th wire:click="sortBy('product_name')" class="nk-tb-col cursor-pointer">
                                <span class="sub-text">Article</span>
                                @if($sortField === 'product_name') <x-sort-icon :direction="$sortDirection" /> @endif
                            </th>
                            <th wire:click="sortBy('amount')" class="nk-tb-col cursor-pointer">
                                <span class="sub-text">Montant</span>
                                @if($sortField === 'amount') <x-sort-icon :direction="$sortDirection" /> @endif
                            </th>
                            <th class="nk-tb-col">Style d'achat</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col nk-tb-col-check">
                                    <div class="custom-control custom-control-sm custom-checkbox notext">
                                        <input type="checkbox" class="custom-control-input" id="pid-{{ $item->id }}">
                                        <label class="custom-control-label" for="pid-{{ $item->id }}"></label>
                                    </div>
                                </td>
                                <td class="nk-tb-col">
                                    <a href="#" class="project-title">
                                        <div class="user-avatar sm bg-blue">
                                            <img src="{{ asset($item->image_url ?? 'assets/images/nophone.jpg') }}" alt="">
                                        </div>
                                        <div class="project-info">
                                            <h6 class="title">{{ optional($item->purchase->customer->user)->name ?? '-' }}</h6>
                                        </div>
                                    </a>
                                </td>
                                <td class="nk-tb-col">{{ optional($item->purchase->customer->user)->phone ?? '-' }}</td>
                                <td class="nk-tb-col">{{ optional($item->purchase->customer)->localisation ?? '-' }}</td>
                                <td class="nk-tb-col">{{ optional($item->purchase->customer)->code_commercial ?? '-' }}</td>
                                <td class="nk-tb-col">
                                    {{ optional($item->purchase->product)->name ?? optional($item->purchase->customProduct)->name ?? '-' }}
                                </td>
                                <td class="nk-tb-col">
                                    {{ number_format(optional($item->purchase->product)->price ?? optional($item->purchase->customProduct)->amount ?? 0, 0, ',', ' ') }} F
                                </td>
                                <td class="nk-tb-col">
                                        <span class="badge bg-outline-primary">
                                            {{ ucfirst($item->purchase->pay_type ?? 'standard') }}
                                        </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Aucun achat trouvé</td>
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

