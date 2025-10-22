<div class="nk-content-wrap">
    <div class="nk-block-head">
        <div class="nk-block-between align-items-center">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">📊 États des crédits</h3>
                <p class="text-soft">Total : {{ $items->total() }}</p>
            </div>

            {{-- 🔹 Filtre de statut --}}
            <div class="nk-block-head-content">
                <select wire:model.live="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="Soldé">Soldé</option>
                    <option value="En cours">En cours</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-stretch mt-3">
        <div class="card-inner p-0">
            <div class="card-body table-responsive">
                <table class="table nk-tb-list nk-tb-ulist">
                    <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th>Nom du client</th>
                        <th>Téléphone</th>
                        <th>Compte Crédit</th>
                        <th>Total (FCFA)</th>
                        <th>Payé (FCFA)</th>
                        <th>Reste (FCFA)</th>
                        <th>% payé</th>
                        <th>Statut</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item['customer'] }}</td>
                            <td>{{ $item['phone'] }}</td>
                            <td>#{{ $item['credit_account'] }}</td>
                            <td>{{ number_format($item['total'], 0, ',', ' ') }}</td>
                            <td>{{ number_format($item['paid'], 0, ',', ' ') }}</td>
                            <td>{{ number_format($item['remaining'], 0, ',', ' ') }}</td>
                            <td>{{ $item['percent'] }}%</td>
                            <td>
                                @if($item['status'] === 'Soldé')
                                    <span class="badge bg-success">Soldé</span>
                                @else
                                    <span class="badge bg-warning text-dark">En cours</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                Aucun crédit trouvé
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{-- 🔹 Pagination --}}
                <div class="mt-3">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


