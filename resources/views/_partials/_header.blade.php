<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="{{route('dashboard')}}" class="logo-link">
                    <img class="logo-light logo-img" src="{{asset('assets/images/logo.png')}}" alt="logo">
                    <img class="logo-dark logo-img" src="{{asset('assets/images/logo.png')}}"  alt="logo-dark">
                </a>
            </div><!-- .nk-header-brand -->
            <div class="nk-header-news d-none d-xl-block">
                <div class="nk-news-list">
                    <a class="nk-news-item" href="#">
                        <div class="nk-news-icon">
                            <em class="icon ni ni-card-view"></em>
                        </div>
                        <div class="nk-news-text">
                            <p>Do you know the latest update of 2025? <span> A overview of our is now available on YouTube</span></p>
                            <em class="icon ni ni-external"></em>
                        </div>
                    </a>
                </div>
            </div>

            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li class="dropdown language-dropdown d-none d-sm-block me-n1">
                        @if( session()->get('locale')=='en')
                        <a href="{{ url('lang/en') }}" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                            <div class="quick-icon border border-light">
                                <img class="icon" src="{{asset('assets/images/flag/english.png')}}" alt="">
                            </div>
                        </a>
                        @else
                            <a href="{{ url('lang/fr') }}" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                                <div class="quick-icon border border-light">
                                    <img class="icon" src="{{asset('assets/images/flag/french.png')}}" alt="">
                                </div>
                            </a>
                            @endif
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-s1">
                            <ul class="language-list">
                                <li>
                                    <a href="{{ url('lang/en') }}" class="language-item">
                                        <img class="language-flag" src="{{asset('assets/images/flag/english.png')}}" alt="">
                                        <span class="language-name">English</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ url('lang/fr') }}" class="language-item">
                                        <img class="language-flag" src="{{asset('assets/images/flag/french.png')}}" alt="">
                                        <span class="language-name">Français</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li><!-- .dropdown -->
                    <li class="dropdown chats-dropdown hide-mb-xs">
                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                            <div class="icon-status icon-status-na"><em class="icon ni ni-comments"></em></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end">
                            <div class="dropdown-head">
                                <span class="sub-title nk-dropdown-title">{{ __('header.recent_chats') }}</span>
                                <a href="#">{{ __('header.settings') }}</a>
                            </div>
                            <div class="dropdown-body">
                                <ul class="chat-list">
                                </ul><!-- .chat-list -->
                            </div><!-- .nk-dropdown-body -->
                            <div class="dropdown-foot center">
                                <a href="#">View All</a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown notification-dropdown">
                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                            <div class="icon-status icon-status-info"><em class="icon ni ni-bell"></em></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end">
                            <div class="dropdown-head">
                                <span class="sub-title nk-dropdown-title">{{ __('header.notifications') }}</span>
                                <a href="#">{{ __('header.mark_all') }}</a>
                            </div>
                            <div class="dropdown-body">
                                <div class="nk-notification">
                                </div><!-- .nk-notification -->
                            </div><!-- .nk-dropdown-body -->
                            <div class="dropdown-foot center">
                                <a href="#">View All</a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle me-n1" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm">
                                    <em class="icon ni ni-user-alt"></em>
                                </div>
                                <div class="user-info d-none d-xl-block">
                                    <div class="user-status user-status-unverified">{{ __('header.unverified') }}</div>
                                    <div class="user-name dropdown-indicator">{!! auth()->user()->name !!}</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <span>AB</span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{!! auth()->user()->name !!}</span>
                                        <span class="sub-text">{!! auth()->user()->email !!}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="#"><em class="icon ni ni-user-alt"></em><span>{{ __('header.view_profile') }}</span></a></li>
                                    <li><a href="#"><em class="icon ni ni-setting-alt"></em><span>{{ __('header.account_setting') }}</span></a></li>
                                    <li><a class="dark-switch" href="#"><em class="icon ni ni-moon"></em><span>{{ __('header.dark_mode') }}</span></a></li>
                                    <li><a href="{{route('signout')}}"><em class="icon ni ni-signout"></em><span>{{ __('header.sign_out') }}</span></a></li>

                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="{{route('signout')}}"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>
