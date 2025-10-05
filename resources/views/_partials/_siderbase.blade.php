<div class="nk-sidebar nk-sidebar-fixed " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="html/index.html" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{asset('assets/images/logo.png')}}" srcset="{{asset('assets/images/logo.png')}} 2x" alt="logo">
                <img class="logo-dark logo-img" src="{{asset('assets/images/logo.png')}}" srcset="{{asset('assets/images/logo.png')}} 2x" alt="logo-dark">
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
        </div>
    </div>
    <!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-body" data-simplebar>
            <div class="nk-sidebar-content">
                <div class="nk-sidebar-menu">
                    <ul class="nk-menu">
                        <li class="nk-menu-item">
                            <a href="{{route('dashboard')}}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-bitcoin-cash"></em></span>
                                <span class="nk-menu-text">Dashboard</span>
                            </a>
                        </li><!-- .nk-menu-item -->

                        <li class="nk-menu-item">
                            <a href="{{route('vendors')}}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                <span class="nk-menu-text">Vendeurs</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{route('partners')}}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-user-cross-fill"></em></span>
                                <span class="nk-menu-text">Partenaires</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{route('purchases')}}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-money"></em></span>
                                <span class="nk-menu-text">Achats</span>
                            </a>
                        </li>
                    </ul><!-- .nk-menu -->
                </div><!-- .nk-sidebar-menu -->
                <div class="nk-sidebar-footer">
                    <ul class="nk-menu nk-menu-footer">
                        <li class="nk-menu-item">
                            <a href="#" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-help-alt"></em></span>
                                <span class="nk-menu-text">Support</span>
                            </a>
                        </li>
                        <li class="nk-menu-item ms-auto">
                            <div class="dropup">
                                @if( session()->get('locale')=='en')
                                    <a href="{{ url('lang/en') }}" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                                        <span class="nk-menu-icon"><em class="icon ni ni-globe"></em></span>
                                        <span class="nk-menu-text">English</span>
                                    </a>
                                @else
                                    <a href="{{ url('lang/fr') }}" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                                        <span class="nk-menu-icon"><em class="icon ni ni-globe"></em></span>
                                        <span class="nk-menu-text">Français</span>
                                    </a>
                                @endif

                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                    <ul class="language-list">
                                        <li>
                                            <a href="{{ url('lang/en') }}" class="language-item">
                                                <img src="{{asset('assets/images/flag/english.png')}}" alt="" class="language-flag">
                                                <span class="language-name">English</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('lang/fr') }}" class="language-item">
                                                <img src="{{asset('assets/images/flag/french.png')}}" alt="" class="language-flag">
                                                <span class="language-name">Français</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul><!-- .nk-footer-menu -->
                </div><!-- .nk-sidebar-footer -->
            </div><!-- .nk-sidebar-content -->
        </div><!-- .nk-sidebar-body -->
    </div><!-- .nk-sidebar-element -->

</div>

