@extends("layout")

@section('title', 'PMEs')

@section('content')
    <div class="nk-content-wrap">

        {{-- HEADER --}}
        <div class="nk-block-head">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">PMEs</h3>
                    <div class="nk-block-des text-soft">
                        <p>Nombre de PMEs : {{ $items->total() }}</p>
                    </div>
                </div>

                <div class="nk-block-head-content">
                    <a href="{{ route('pme.create') }}" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Ajouter</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card card-bordered card-stretch">
            <div class="card-inner-group">
                <div class="card-inner p-0">
                    <div class="card-body">

                        <table class="nk-tb-list nk-tb-ulist">
                            <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col">User</th>
                                <th class="nk-tb-col">Référence</th>
                                <th class="nk-tb-col">Entreprise</th>
                                <th class="nk-tb-col">Montant total</th>
                                <th class="nk-tb-col">Statut</th>
                                <th class="nk-tb-col text-end">Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($items as $item)
                                <tr class="nk-tb-item">
                                    <td>{{ $item->user->name ?? '-' }}</td>

                                    <td class="nk-tb-col">
                                        {{ $item->referenceId }}
                                    </td>

                                    <td class="nk-tb-col">
                                        {{ $item->name_entreprise }}
                                    </td>

                                    <td class="nk-tb-col">
                                        {{ number_format($item->montant_total, 0, ',', ' ') }} F
                                    </td>

                                    <td class="nk-tb-col">
                                    <span class="badge
                                        @if($item->status === 'confirmed') bg-success
                                        @elseif($item->status === 'failed') bg-danger
                                        @else bg-warning @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    </td>

                                    <td class="nk-tb-col text-end">
                                        <a href="{{ route('pmes.show', $item) }}"
                                           class="btn btn-sm btn-info">
                                            Voir
                                        </a>

                                        <a href="{{ route('pmes.edit', $item) }}"
                                           class="btn btn-sm btn-warning">
                                            Éditer
                                        </a>

                                        <form action="{{ route('pmes.destroy', $item) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Supprimer cette PME ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aucune PME trouvée
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        {{-- PAGINATION --}}
                        <div class="mt-3">
                            {{ $items->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
