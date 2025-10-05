@extends('security.layout')
@section('title')
    {{ __('auth.signin_title') }}
@endsection
@section('content')
    <div class="nk-content ">
        <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
            <div class="brand-logo pb-4 text-center">
                <a href="{!! route('login') !!}" class="logo-link">
                    <img class="logo-light logo-img logo-img-lg" src="{!! asset('assets/images/logo.png') !!}" alt="logo">
                    <img class="logo-dark logo-img logo-img-lg" src="{!! asset('assets/images/logo.png') !!}"  alt="logo-dark">
                </a>
            </div>
            <div class="card card-bordered">
                <div class="card-inner card-inner-lg">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">{{ __('auth.signin_title') }}</h4>
                        <div class="nk-block-des">
                            <p>{{ __('auth.welcome') }}</p>
                        </div>
                    </div>

                    <form method="POST">
                        @csrf
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="default-01">{{ __('auth.email') }}</label>
                            </div>
                            <div class="form-control-wrap">
                                <input name="email" type="text" class="form-control form-control-lg" id="default-01"
                                       placeholder="{{ __('auth.email_placeholder') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="password">{{ __('auth.password') }}</label>
                                <a class="link link-primary link-sm" href="#">{{ __('auth.forgot') }}</a>
                            </div>
                            <div class="form-control-wrap">
                                <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                </a>
                                <input name="password" type="password" class="form-control form-control-lg" id="password" placeholder="{{ __('auth.password_placeholder') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-lg btn-primary btn-block">{{ __('auth.button') }}</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
        <div class="nk-footer nk-auth-footer-full">
            <div class="container wide-lg">
                <div class="row g-3">
                    <div class="col-lg-6 order-lg-last">
                        <ul class="nav nav-sm justify-content-center justify-content-lg-end">
                            <li class="nav-item dropup">
                                <a class="dropdown-toggle dropdown-indicator has-indicator link link-primary fw-normal py-2 px-3" data-bs-toggle="dropdown" data-offset="0,10"><span>English</span></a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                    <ul class="language-list">
                                        <li>
                                            <a href="{{ route('lang.switch', 'en') }}" class="language-item">
                                                <img src="{{asset('flags/gb-eng.png')}}" alt="" class="language-flag">
                                                <span class="language-name">English</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('lang.switch', 'fr') }}" class="language-item">
                                                <img src="{{asset('flags/fr.png')}}" alt="" class="language-flag">
                                                <span class="language-name">Français</span>
                                            </a>
                                        </li>


                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <div class="nk-block-content text-center text-lg-left">
                            <p class="text-soft">{!! __('auth.footer', ['year' => date('Y')]) !!}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
