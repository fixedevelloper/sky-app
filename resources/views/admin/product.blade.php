@extends("layout")
@section('siederbar')

@endsection

@section('title', 'Partenaires')

@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Produits</h3>
                    <div class="nk-block-des text-soft">
                        <p>Nombre de produits {{count($items)}}</p>
                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt d-none d-sm-block">
                                    <a href="#" data-target="addProduct" class="toggle btn btn-primary"><em class="icon ni ni-plus"></em><span>Ajouter</span></a>
                                </li>
                                <li class="nk-block-tools-opt d-block d-sm-none">
                                    <a href="#" data-target="addProduct" class="toggle btn btn-icon btn-primary"><em class="icon ni ni-plus"></em></a>
                                </li>
                            </ul>
                        </div>
                    </div><!-- .toggle-wrap -->
                </div>
            </div>
        </div>
        <div class="card card-bordered card-stretch">
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
                                <th class="nk-tb-col"><span class="sub-text">Nom du produit</span></th>
                                <th class="nk-tb-col tb-col-xxl"><span class="sub-text">Categorie</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">prix</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">Prix leasing</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">Memoire</span></th>
                                <th class="nk-tb-col nk-tb-col-tools text-end">
                                </th>
                            </tr><!-- .nk-tb-item -->
                            </thead>
                            <tbody>
                            @foreach($items as $item)
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col nk-tb-col-check">
                                        <div class="custom-control custom-control-sm custom-checkbox notext">
                                            <input type="checkbox" class="custom-control-input" id="pid-01">
                                            <label class="custom-control-label" for="pid-01"></label>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col">
                                        <a href="#" class="project-title">
                                            <div class="user-avatar sm bg-blue"><img src="{{asset($item->image_url)}}" alt=""></div>
                                            <div class="project-info">
                                                <h6 class="title">{{$item->name}}</h6>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{$item->category->name}}</span>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{$item->price}}</span>
                                    </td>

                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{$item->price_leasing}}</span>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{$item->memory}}</span>
                                    </td>
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <ul class="link-list-opt no-bdr">
                                                          <li><a href="#"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                                            <li><a href="#"><em class="icon ni ni-check-round-cut"></em><span>Supprimer</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div></div>
    </div>
    <div class="nk-add-product toggle-slide toggle-slide-right" data-content="addProduct" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar>
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">Creer un produit</h5>
                <div class="nk-block-des">
                    <p>Ajouter les informations du produit.</p>
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <form method="POST" enctype="multipart/form-data">
                @csrf
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label" for="product-title">Nom du produit</label>
                        <div class="form-control-wrap">
                            <input type="text" name="name" class="form-control" id="product-title">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label" for="product-title">Prix du cash</label>
                        <div class="form-control-wrap">
                            <input type="text" name="price" class="form-control" id="product-title">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label" for="product-title">Prix du leasing</label>
                        <div class="form-control-wrap">
                            <input type="text" name="price_leasing" class="form-control" id="product-title">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label" for="product-title">Memoire</label>
                        <div class="form-control-wrap">
                            <input type="text" name="memory" class="form-control" id="product-title">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="default-07">Categorie</label>
                    <div class="form-control-wrap">
                        <div class="form-control-select-multiple">
                            <select name="category_id" class="form-select" id="default-07" aria-label="multiple select example">
                                @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <div class="form-control-wrap">
                    <input type="file" name="image_url" class="form-control">
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Ajouter</span></button>
                </div>
            </div>
            </form>
        </div><!-- .nk-block -->
    </div>
@endsection
