@extends("layout")

@section('title', 'Créer une PME')

@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Créer une PME</h3>
                    <div class="nk-block-des text-soft">
                        <p>Ajouter une nouvelle PME</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <form method="POST" action="{{ route('pmes.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">Utilisateur associé</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Sélectionner un utilisateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ implode(', ', $user->roles ?? []) }})
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        {{-- Entreprise --}}
                        <div class="col-md-6">
                            <label class="form-label">Nom de l’entreprise</label>
                            <input type="text" name="name_entreprise" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Opérateur</label>
                            <select name="operator" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                <option value="MTN">MTN</option>
                                <option value="ORANGE">ORANGE</option>
                            </select>
                        </div>

                        {{-- Responsable --}}
                        <div class="col-md-4">
                            <label class="form-label">Nom du responsable</label>
                            <input type="text" name="name_responsable" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Poste du responsable</label>
                            <input type="text" name="poste_responsable" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Gestionnaire</label>
                            <input type="text" name="name_gestionnaire" class="form-control" required>
                        </div>

                        {{-- Finances --}}
                        <div class="col-md-4">
                            <label class="form-label">Montant BC</label>
                            <input type="number" step="0.01" name="amount_bc" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre de souscripteurs</label>
                            <input type="number" name="number_souscripteur" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre d’échéances</label>
                            <input type="number" name="number_echeance_paiement" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Montant total</label>
                            <input type="number" step="0.01" name="montant_total" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Manager</label>
                            <input type="text" name="name_manager" class="form-control" required>
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
                                <input type="file" name="{{ $name }}" class="form-control" required>
                            </div>
                        @endforeach

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
