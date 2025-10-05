@extends("layout")
@section('siederbar')

@endsection

@section('title', 'Point de ventes')

@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Point de ventes de {{ $vendor->name }}</h3>
                    <div class="nk-block-des text-soft">
                        <p>Nombre de point de ventes {{count($items)}}</p>
                    </div>
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
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
                            <th class="nk-tb-col"><span class="sub-text">Nom de point de vente</span></th>
                            <th class="nk-tb-col tb-col-lg"><span class="sub-text">Localisation</span></th>
                            <th class="nk-tb-col tb-col-lg"><span class="sub-text">Image</span></th>
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
                                <span>{{$item->localisation}}</span>
                            </td>
                            <td class="nk-tb-col tb-col-xxl">
                                <img class="img-thumbnail" width="80" height="80" src="{{asset($item->image_doc_fiscal)}}" alt="">
                            </td>
                            <td class="nk-tb-col nk-tb-col-tools">
                                <ul class="nk-tb-actions gx-1">
                                    <li>
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#"><em class="icon ni ni-eye"></em><span>Points de vente</span></a></li>
                                                    <li><a href="#"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
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
@endsection



