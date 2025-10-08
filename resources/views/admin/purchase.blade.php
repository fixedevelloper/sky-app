@extends("layout")
@section('siederbar')

@endsection

@section('title', 'Achats')

@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Achats</h3>
                    <div class="nk-block-des text-soft">
                        <p>Nombre de achats {{count($items)}}</p>
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
                                <th class="nk-tb-col"><span class="sub-text">Client</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">Product</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">Prix</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">N Tel</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">Localisation</span></th>
                                <th class="nk-tb-col tb-col-lg"><span class="sub-text">Point de vente</span></th>
                                <th class="nk-tb-col nk-tb-col-tools text-end">

                                </th>
                            </tr><!-- .nk-tb-item -->
                            </thead>
                            <tbody>
                            @foreach($items as $item)
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
                                                <h6 class="title">{{ $item->customer->name }}</h6>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{ optional($item->product)->name ?? optional($item->customProduct)->name ?? '-' }}</span>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{ optional($item->product)->price ?? optional($item->customProduct)->amount ?? '-' }}</span>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{ $item->customer->phone }}</span>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{ $item->localisation ?? '-' }}</span>
                                    </td>
                                    <td class="nk-tb-col tb-col-xxl">
                                        <span>{{ optional($item->customer->pointSale)->name ?? '-' }}</span>
                                    </td>
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li><a href="{{ route('paiements',['id'=>$item->id]) }}"><em class="icon ni ni-eye"></em><span>Paiements</span></a></li>
                                                            <li><a href="#"><em class="icon ni ni-edit"></em><span>Details</span></a></li>
                                                        {{--    @if($item->customProduct)
                                                                <li><a href="{{ route('custom.products.show', $item->customProduct->id) }}"><em class="icon ni ni-eye"></em><span>Custom Product</span></a></li>
                                                            @endif--}}
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



