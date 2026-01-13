@extends('layout')

@section('title', 'Éditer PME')

@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Éditer PME</h3>
                    <div class="nk-block-des text-soft">
                        <p>Modifier les informations de la PME</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <form method="POST" action="{{ route('pmes.update', $item) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        {{-- Utilisateur --}}
                        <div class="col-md-6">
                            <label class="form-label">Utilisateur associé</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Sélectionner un utilisateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $item->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ implode(', ', $user->roles ?? []) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Opérateur --}}
                        <div class="col-md-6">
                            <label class="form-label">Opérateur</label>
                            <select name="operator" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                <option value="MTN" {{ $item->operator === 'MTN' ? 'selected' : '' }}>MTN</option>
                                <option value="ORANGE" {{ $item->operator === 'ORANGE' ? 'selected' : '' }}>ORANGE</option>
                            </select>
                        </div>

                        {{-- Entreprise et Responsable --}}
                        <div class="col-md-6">
                            <label class="form-label">Nom de l’entreprise</label>
                            <input type="text" name="name_entreprise" class="form-control" value="{{ $item->name_entreprise }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nom du responsable</label>
                            <input type="text" name="name_responsable" class="form-control" value="{{ $item->name_responsable }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Poste du responsable</label>
                            <input type="text" name="poste_responsable" class="form-control" value="{{ $item->poste_responsable }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gestionnaire</label>
                            <input type="text" name="name_gestionnaire" class="form-control" value="{{ $item->name_gestionnaire }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Manager</label>
                            <input type="text" name="name_manager" class="form-control" value="{{ $item->name_manager }}" required>
                        </div>

                        {{-- Finances --}}
                        <div class="col-md-4">
                            <label class="form-label">Montant BC</label>
                            <input type="number" step="0.01" name="amount_bc" class="form-control" value="{{ $item->amount_bc }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre de souscripteurs</label>
                            <input type="number" name="number_souscripteur" class="form-control" value="{{ $item->number_souscripteur }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre d’échéances</label>
                            <input type="number" name="number_echeance_paiement" class="form-control" value="{{ $item->number_echeance_paiement }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Montant total</label>
                            <input type="number" step="0.01" name="montant_total" class="form-control" value="{{ $item->montant_total }}" required>
                        </div>

                        {{-- Documents --}}
                        @php
                            $files = [
                                'image_bc' => 'BC',
                                'image_bl' => 'BL',
                                'image_facture' => 'Facture',
                                'image_avi' => 'AVI',
                                'image_pl' => 'PL',
                                'image_contract1' => 'Contrat 1',
                                'image_contract2' => 'Contrat 2',
                            ];
                        @endphp

                        @foreach($files as $name => $label)
                            <div class="col-md-6">
                                <label class="form-label">{{ $label }}</label>
                                <input type="file" name="{{ $name }}" class="form-control">
                                @if($item->$name)
                                    <small class="text-muted">Fichier actuel : <a href="{{ asset('storage/' . $item->$name) }}" target="_blank">Voir</a></small>
                                @endif
                            </div>
                        @endforeach

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $item->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="failed" {{ $item->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>

                        {{-- Actions --}}
                        <div class="col-12 text-end">
                            <a href="{{ route('pmes.index') }}" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <em class="icon ni ni-save"></em> Enregistrer
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
