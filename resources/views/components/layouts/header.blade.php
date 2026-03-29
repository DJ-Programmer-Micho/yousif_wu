{{-- resources/views/components/layouts/header.blade.php --}}
<header class="topbar app-topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md app-topbar-nav-shell">
        <div class="navbar-header app-topbar-brand-shell" style="box-shadow: unset; -webkit-box-shadow: unset" data-logobg="skin6">

            {{-- Mobile sidebar toggle --}}
            <a class="nav-toggler waves-effect waves-light d-block d-md-none app-topbar-action" href="javascript:void(0)">
                <i class="ti-menu ti-close"></i>
            </a>

            {{-- Logo --}}
            <div class="navbar-brand">
                <a href="/">
                    <b class="logo-icon">
                        <img src="../assets/images/logo-icon.png" alt="{{ __('Homepage') }}" class="dark-logo" />
                        <img src="../assets/images/logo-icon.png" alt="{{ __('Homepage') }}" class="light-logo" />
                    </b>
                    <span class="logo-text">
                        <img src="../assets/images/logo-text-white.png" alt="{{ __('Homepage') }}" class="dark-logo" width="165" />
                        <img src="../assets/images/logo-text-white.png" class="light-logo" alt="{{ __('Homepage') }}" width="165" />
                    </span>
                </a>
            </div>

            {{-- Mobile top-bar toggle --}}
            <a class="topbartoggler d-block d-md-none waves-effect waves-light app-topbar-action ml-auto"
               href="javascript:void(0)"
               data-toggle="collapse"
               data-target="#navbarSupportedContent"
               aria-controls="navbarSupportedContent"
               aria-expanded="false"
               aria-label="{{ __('Toggle navigation') }}">
                <i class="ti-more"></i>
            </a>
        </div>

        <div class="navbar-collapse collapse" id="navbarSupportedContent" style="padding: 0; border-bottom: 0px solid #edf2f9;">

            {{-- Left-side actions --}}
            <ul class="navbar-nav float-left ml-auto ml-3 pl-1 app-topbar-right" style="gap: 6px; display: flex; align-items: center;">

                {{-- Notification bell --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle pl-md-3 position-relative app-topbar-action"
                        style="line-height: unset"
                       href="javascript:void(0)"
                       id="bell"
                       role="button"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-bell svg-icon">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </span>
                        <span class="badge badge-primary notify-no rounded-circle">1</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                        <ul class="list-style-none">
                            <li>
                                <div class="message-center notifications position-relative ps-container ps-theme-default"
                                     data-ps-id="8d291162-4434-c402-4ca3-658d9e6779a4">
                                    <a href="javascript:void(0)" class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                        <div class="btn btn-danger rounded-circle btn-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                 stroke-linejoin="round" class="feather feather-airplay text-white">
                                                <path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path>
                                                <polygon points="12 15 17 21 7 21 12 15"></polygon>
                                            </svg>
                                        </div>
                                        <div class="w-75 d-inline-block v-middle pl-2">
                                            <h6 class="message-title mb-0 mt-1">{{ __('Luanch Admin') }}</h6>
                                            <span class="font-12 text-nowrap d-block text-muted">{{ __('New Register Added in System!') }}</span>
                                            <span class="font-12 text-nowrap d-block text-muted">{{ __('9:30 AM') }}</span>
                                        </div>
                                    </a>
                                    <div class="ps-scrollbar-x-rail" style="left:0;bottom:0">
                                        <div class="ps-scrollbar-x" tabindex="0" style="left:0;width:0"></div>
                                    </div>
                                    <div class="ps-scrollbar-y-rail" style="top:0;right:3px">
                                        <div class="ps-scrollbar-y" tabindex="0" style="top:0;height:0"></div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a class="nav-link pt-3 text-center text-dark" href="javascript:void(0);">
                                    <strong>{{ __('No Notifications') }}</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Settings dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle pl-md-3 position-relative app-topbar-action"
                    style="line-height: unset"
                       href="#"
                       id="navbarDropdown"
                       role="button"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-settings svg-icon">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('setting') }}">{{ __('Receiver Actions') }}</a>
                    </div>
                </li>

                {{-- Language switcher --}}
                <li class="nav-item d-none d-md-block">
                    <a class="nav-link app-topbar-language" href="javascript:void(0)" style="line-height: unset">
                        <div class="customize-input">
                            <select class="custom-select form-control bg-white custom-radius custom-shadow border-0 app-language-switcher p-1"
                                    style="line-height: 2"
                                    id="language-switcher">
                                <option value="en" @if(app()->getLocale() == 'en') selected @endif>{{ __('English') }}</option>
                                <option value="ar" @if(app()->getLocale() == 'ar') selected @endif>{{ __('Arabic') }}</option>
                                <option value="ku" @if(app()->getLocale() == 'ku') selected @endif>{{ __('Kurdish') }}</option>
                            </select>
                        </div>
                    </a>
                </li>
            </ul>

            {{-- Right-side: User profile --}}
            <ul class="navbar-nav float-right app-topbar-right" style="gap: 6px; display: flex; align-items: center;">
                <li class="nav-item dropdown">

                    {{-- Profile button — bevel chip with avatar --}}
                    <a class="nav-link dropdown-toggle app-topbar-profile d-flex align-items-center"
                       href="javascript:void(0)"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false"
                       style="gap: 8px; text-decoration: none; line-height:unset">

                        {{-- Avatar with its own raised border --}}
                        <span style="
                            display: inline-flex;
                            border-radius: 50%;
                            padding: 2px;
                            background: linear-gradient(145deg, rgba(255,255,255,.30) 0%, rgba(15,5,40,.30) 100%);
                            box-shadow:
                                inset 0 1px 0 rgba(255,255,255,.20),
                                inset 0 -1px 0 rgba(15,5,40,.28),
                                0 2px 6px rgba(15,5,40,.22);
                        ">
                            <img src="{{ auth()->user()->profile?->avatar
                                ? app('cloudfront').auth()->user()->profile->avatar
                                : app('cloudfront').'avatar/user.png' }}"
                                 alt="{{ __('User') }}"
                                 class="rounded-circle"
                                 width="36"
                                 height="36"
                                 style="border-radius:50%; display:block;">
                        </span>

                        {{-- Name — hidden on mobile --}}
                        <span class="d-none d-lg-inline-flex align-items-center" style="gap: 4px;">
                            <span style="font-size: .82rem; color: rgba(255,255,255,.72);">{{ __('Hello,') }}</span>
                            <span style="font-size: .88rem; font-weight: 700; color: #fff;">{{ auth()->user()->name }}</span>
                            <i data-feather="chevron-down" class="svg-icon" style="width:14px;height:14px;color:#fff;"></i>
                        </span>
                    </a>

                    {{-- Profile dropdown: force right-align and top-offset on all screen sizes --}}
                    <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY"
                         style="margin-top: 8px; min-width: 200px;">
                        <a class="dropdown-item text-primary" href="{{ route('profile') }}">
                            <i data-feather="user" class="svg-icon mr-2 ml-1"></i>
                            {{ __('My Profile') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{ route('auth.logout') }}">
                            <i data-feather="power" class="svg-icon mr-2 ml-1"></i>
                            {{ __('Logout') }}
                        </a>
                    </div>
                </li>
            </ul>

        </div>
    </nav>
</header>