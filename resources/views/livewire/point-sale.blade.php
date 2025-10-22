<div class="nk-content-wrap">
    <div class="nk-block-head">
        <div class="nk-block-between align-items-center">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Points de vente</h3>
                <p class="text-soft">Nombre total : {{ $items->total() }}</p>
            </div>

            {{-- 🔍 Barre de recherche --}}
            <div class="nk-block-head-content">
                <input type="text" wire:model.live.debounce.200ms="search" class="form-control"
                       placeholder="Rechercher par nom, promoteur ou téléphone...">
            </div>
        </div>
    </div>

    <div class="card card-bordered card-stretch mt-3">
        <div class="card-inner-group">
            <div class="card-inner p-0">
                <div class="card-body">
                    <table class="nk-tb-list nk-tb-ulist table">
                        <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th wire:click="sortBy('vendor_id')" class="nk-tb-col cursor-pointer">
                                    <span class="sub-text">Promoteur
                                        @if($sortField === 'vendor_id')
                                            <x-sort-icon :direction="$sortDirection" />
                                        @endif
                                    </span>
                            </th>
                            <th wire:click="sortBy('name')" class="nk-tb-col cursor-pointer">
                                <span class="sub-text">Nom du point</span>
                                @if($sortField === 'name')
                                    <x-sort-icon :direction="$sortDirection" />
                                @endif
                            </th>
                            <th class="nk-tb-col">Téléphone</th>
                            <th wire:click="sortBy('activity')" class="nk-tb-col cursor-pointer">
                                <span class="sub-text">Activité</span>
                                @if($sortField === 'activity')
                                    <x-sort-icon :direction="$sortDirection" />
                                @endif
                            </th>
                            <th class="nk-tb-col">Adresse</th>
                            <th class="nk-tb-col">Montant</th>
                            <th wire:click="sortBy('status')" class="nk-tb-col cursor-pointer">
                                <span class="sub-text">Statut</span>
                                @if($sortField === 'status')
                                    <x-sort-icon :direction="$sortDirection" />
                                @endif
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col">
                                    <div class="user-avatar sm bg-blue">
                                        <img src="{{ asset($item->image_url) }}" alt="">
                                    </div>
                                    <div class="project-info">
                                        <h6 class="title">{{ $item->vendor->name ?? '-' }}</h6>
                                    </div>
                                </td>
                                <td class="nk-tb-col">{{ $item->name }}</td>
                                <td class="nk-tb-col">{{ $item->vendor->phone ?? '-' }}</td>
                                <td class="nk-tb-col">{{ $item->vendor->activity ?? '-' }}</td>
                                <td class="nk-tb-col">{{ $item->localisation }}</td>
                                <td class="nk-tb-col">15 000 F</td>
                                <td class="nk-tb-col">{{ $item->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">Aucun résultat trouvé</td></tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{-- 🔹 PAGINATION --}}
                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🔹 Petit composant Blade pour icône de tri --}}
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


