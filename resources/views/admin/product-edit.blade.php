@extends("layout")
@section('siederbar')

@endsection

@section('title', 'Editer un product')
@section('content')

    <div class="nk-content-wrap">
        <div class="nk-block-head">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Product {{ $product->name }}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
        <div class="card card-bordered card-stretch">
            <div class="card-inner-group">
                <div class="card-inner p-0">
                    <div class="card-body">
                        <form method="POST"
                              action="{{ route('admin.products.update', $product) }}"
                              enctype="multipart/form-data">

                            @csrf

                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label">Nom du produit</label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name', $product->name) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Mémoire</label>
                                    <input type="text" name="memory" class="form-control"
                                           value="{{ old('memory', $product->memory) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Prix Cash</label>
                                    <input type="number" name="price" step="0.01"
                                           class="form-control"
                                           value="{{ old('price', $product->price) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Prix Commercial</label>
                                    <input type="number" name="price_commercial" step="0.01"
                                           class="form-control"
                                           value="{{ old('price_commercial', $product->price_commercial) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Prix Point de vente</label>
                                    <input type="number" name="price_leasing" step="0.01"
                                           class="form-control"
                                           value="{{ old('price_leasing', $product->price_leasing) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Prix Distributeur</label>
                                    <input type="number" name="price_distribute" step="0.01"
                                           class="form-control"
                                           value="{{ old('price_distribute', $product->price_distribute) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Prix PME</label>
                                    <input type="number" name="price_pme" step="0.01"
                                           class="form-control"
                                           value="{{ old('price_pme', $product->price_pme) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Catégorie</label>
                                    <select name="category_id" class="form-select">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                    @selected(old('category_id', $product->category_id) == $category->id)>
                                            {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image_url" class="form-control">
                                </div>

                                @if($product->image_url)
                                    <div class="col-12">
                                        <img src="{{ $product->image_url }}"
                                             class="img-thumbnail"
                                             width="120">
                                    </div>
                                @endif

                                <div class="col-12">
                                    <button class="btn btn-primary">
                                        <em class="icon ni ni-save"></em>
                                        <span>Mettre à jour</span>
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>

                </div>
            </div></div>
    </div>
@endsection

